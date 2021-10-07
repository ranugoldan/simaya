<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Gate;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetCheckoutRequest;
use App\Http\Transformers\AssetsTransformer;
use App\Http\Transformers\LicensesTransformer;
use App\Http\Transformers\SelectlistTransformer;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Company;
use App\Models\CustomField;
use App\Models\License;
use App\Models\Location;
use App\Models\Procurement;
use App\Models\Setting;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Input;
use Paginator;
use Slack;
use Str;
use TCPDF;
use Validator;


class ProcurementsController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('view', Procurement::class);

        $allowed_columns = [
            'id',
            'procurement_tag',
            'status',
            'model',
            'asset',
            'supplier',
            'qty',
            'purchase_cost',
            'location',
            'department',
            'user',
            'created_at',
        ];

        $procurements = Procurement::with('models', 'assets', 'supplier', 'locations', 'department', 'user')
            ->select([
                'procurements.id',
                'procurements.procurement_tag',
                'procurements.status',
                'procurement_models.model_id',
                'procurement_assets.asset_id',
                'procurements.supplier_id',
                'procurement_models.qty',
                'procurement_models.purchase_cost',
                'procurement_locations.location_id',
                'procurements.department_id',
                'procurements.user_id',
                'procurements.created_at',
            ]);
        
        if ($request->filled('search')) {
            $procurements = $procurements->TextSearch($request->input('search'));
        }

        $offset = (($procurements) && (request('offset') > $procurements->count())) ? 0 : request('offset', 0);

        ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit'))) ? $limit = $request->input('limit') : $limit = config('app.max_results');

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort = in_array($request->input('sort'), $allowed_columns) ? $request->input('sort') : 'created_at';

        switch ($request->input('sort')) {
            case 'model':
                $procurements->OrderModels($order);
                break;
            case 'asset':
                $procurements->OrderAssets($order);
                break;
            case 'supplier':
                $procurements->OrderSupplier($order);
                break;
            case 'location':
                $procurements->OrderLocations($order);
                break;
            case 'department':
                $procurements->OrderDepartment($order);
                break;
            case 'user':
                $procurements->OrderUser($order);
                break;
            default:
                $procurements->orderBy($sort, $order);
                break;
        }

        $total = $procurements->count();
        $procurements = $procurements->skip($offset)->take($limit)->get();
        return (new ProcurementsTransformer)->transformProcurements($procurements, $total);
    }


    /**
     * Returns JSON with information about an asset (by tag) for detail view.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param string $tag
     * @since [v4.2.1]
     * @return JsonResponse
     */
    public function showByTag($tag)
    {
        if ($asset = Asset::with('assetstatus')->with('assignedTo')->where('asset_tag',$tag)->first()) {
            $this->authorize('view', $asset);
            return (new AssetsTransformer)->transformAsset($asset);
        }
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Asset not found'), 200);

    }

    /**
     * Returns JSON with information about an asset (by serial) for detail view.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param string $serial
     * @since [v4.2.1]
     * @return JsonResponse
     */
    public function showBySerial($serial)
    {
        $this->authorize('index', Asset::class);
        if ($procurements = Asset::with('assetstatus')->with('assignedTo')
            ->withTrashed()->where('serial',$serial)->get()) {
                return (new AssetsTransformer)->transformAssets($procurements, $procurements->count());
        }
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Asset not found'), 200);

    }


    /**
     * Returns JSON with information about an asset for detail view.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $assetId
     * @since [v4.0]
     * @return JsonResponse
     */
    public function show($id)
    {
        if ($asset = Asset::with('assetstatus')->with('assignedTo')->withTrashed()
            ->withCount('checkins as checkins_count', 'checkouts as checkouts_count', 'userRequests as user_requests_count')->findOrFail($id)) {
            $this->authorize('view', $asset);
            return (new AssetsTransformer)->transformAsset($asset);
        }


    }


    /**
     * Gets a paginated collection for the select2 menus
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0.16]
     * @see \App\Http\Transformers\SelectlistTransformer
     *
     */
    public function selectlist(Request $request)
    {

        $procurements = Company::scopeCompanyables(Asset::select([
            'assets.id',
            'assets.name',
            'assets.asset_tag',
            'assets.model_id',
            'assets.assigned_to',
            'assets.assigned_type',
            'assets.status_id'
            ])->with('model', 'assetstatus', 'assignedTo')->NotArchived(), 'company_id', 'assets');

        if ($request->filled('assetStatusType') && $request->input('assetStatusType') === 'RTD') {
            $procurements = $procurements->RTD();
        }

        if ($request->filled('search')) {
            $procurements = $procurements->AssignedSearch($request->input('search'));
        }


        $procurements = $procurements->paginate(50);

        // Loop through and set some custom properties for the transformer to use.
        // This lets us have more flexibility in special cases like assets, where
        // they may not have a ->name value but we want to display something anyway
        foreach ($procurements as $asset) {


            $asset->use_text = $asset->present()->fullName;

            if (($asset->checkedOutToUser()) && ($asset->assigned)) {
                $asset->use_text .= ' → '.$asset->assigned->getFullNameAttribute();
            }


            if ($asset->assetstatus->getStatuslabelType()=='pending') {
                $asset->use_text .=  '('.$asset->assetstatus->getStatuslabelType().')';
            }

            $asset->use_image = ($asset->getImageUrl()) ? $asset->getImageUrl() : null;
        }

        return (new SelectlistTransformer)->transformSelectlist($procurements);

    }


    /**
     * Accepts a POST request to create a new asset
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param Request $request
     * @since [v4.0]
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Procurement::class);

        $procurement = new Procurement();
        $procurement->models->associate(AssetModel::find((int) $request->get('model_id')));

        $procurement->procurement_tag   = $request->get('procurement_tag');
        $procurement->status            = $request->get('status');
        $procurement->model_id          = $request->get('model_id');
        $procurement->asset_id          = $request->get('asset_id');
        $procurement->supplier_id       = $request->get('supplier_id');
        $procurement->qty               = $request->get('qty');
        $procurement->purchase_cost     = $request->get('purchase_cost');
        $procurement->location_id       = $request->get('location_id');
        $procurement->department_id     = $request->get('department_id');
        $procurement->user_id           = $request->get('user_id');
        
        if ($procurement->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $procurement, trans('admin/procurements/message.create.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $procurement->getErrors()), 200);
    }


    /**
     * Accepts a POST request to update an asset
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param Request $request
     * @since [v4.0]
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', Asset::class);

        if ($asset = Asset::find($id)) {
            $asset->fill($request->all());

            ($request->filled('model_id')) ?
                $asset->model()->associate(AssetModel::find($request->get('model_id'))) : null;
            ($request->filled('rtd_location_id')) ?
                $asset->location_id = $request->get('rtd_location_id') : '';
            ($request->filled('company_id')) ?
                $asset->company_id = Company::getIdForCurrentUser($request->get('company_id')) : '';

            ($request->filled('rtd_location_id')) ?
                $asset->location_id = $request->get('rtd_location_id') : null;


            if ($request->filled('image_source')) {
                if ($request->input('image_source') == "") {
            ($request->filled('rtd_location_id')) ?
                $asset->location_id = $request->get('rtd_location_id') : null;
                    $asset->image = null;
                } else {
                    $saved_image_path = Helper::processUploadedImage(
                        $request->input('image_source'), 'uploads/assets/'
                    );

                    if (!$saved_image_path) {
                        return response()->json(Helper::formatStandardApiResponse(
                            'error',
                            null,
                            trans('admin/hardware/message.update.error')
                        ), 200);
                    }

                    $asset->image = $saved_image_path;
                }
            }

            // Update custom fields
            if (($model = AssetModel::find($asset->model_id)) && (isset($model->fieldset))) {
                foreach ($model->fieldset->fields as $field) {
                    if ($request->has($field->convertUnicodeDbSlug())) {
                        if ($field->field_encrypted=='1') {
                            if (Gate::allows('admin')) {
                                $asset->{$field->convertUnicodeDbSlug()} = \Crypt::encrypt($request->input($field->convertUnicodeDbSlug()));
                            }
                        } else {
                            $asset->{$field->convertUnicodeDbSlug()} = $request->input($field->convertUnicodeDbSlug());
                        }
                    }
                }
            }


            if ($asset->save()) {

                if (($request->filled('assigned_user')) && ($target = User::find($request->get('assigned_user')))) {
                        $location = $target->location_id;
                } elseif (($request->filled('assigned_asset')) && ($target = Asset::find($request->get('assigned_asset')))) {
                    $location = $target->location_id;

                    Asset::where('assigned_type', '\\App\\Models\\Asset')->where('assigned_to', $id)
                        ->update(['location_id' => $target->location_id]);

                } elseif (($request->filled('assigned_location')) && ($target = Location::find($request->get('assigned_location')))) {
                    $location = $target->id;
                }

                if (isset($target)) {
                    $asset->checkOut($target, Auth::user(), date('Y-m-d H:i:s'), '', 'Checked out on asset update', e($request->get('name')), $location);
                }

                if ($asset->image) {
                    $asset->image = $asset->getImageUrl();
                }

                return response()->json(Helper::formatStandardApiResponse('success', $asset, trans('admin/hardware/message.update.success')));
            }
            return response()->json(Helper::formatStandardApiResponse('error', null, $asset->getErrors()), 200);
        }
        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/hardware/message.does_not_exist')), 200);
    }


    /**
     * Delete a given asset (mark as deleted).
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $assetId
     * @since [v4.0]
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->authorize('delete', Asset::class);

        if ($asset = Asset::find($id)) {

            $this->authorize('delete', $asset);

            DB::table('assets')
                ->where('id', $asset->id)
                ->update(array('assigned_to' => null));

            $asset->delete();

            return response()->json(Helper::formatStandardApiResponse('success', null, trans('admin/hardware/message.delete.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/hardware/message.does_not_exist')), 200);
    }
}
