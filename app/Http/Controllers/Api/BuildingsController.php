<?php

// namespace App\Http\Controllers\Api;

// use App\Helpers\Helper;
// use App\Http\Controllers\Controller;
// use App\Http\Transformers\BuildingsTransformer;
// use App\Http\Transformers\SelectlistTransformer;
// use App\Models\Company;
// use App\Models\Building;
// use App\Models\User;
// use Illuminate\Http\Request;

// class BuildingsController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @since [v4.0]
//      *
//      * @return \Illuminate\Http\Response
//      */
//     public function index(Request $request)
//     {
//         $this->authorize('index', Building::class);
//         $buildings = Company::scopeCompanyables(
//             Building::select('buildings.*')
//                 ->with('company', 'location',
//                 // 'area',
//                 // 'category',
//                 'users',
//                 // 'manufacturer'
//                 )
//         );

//         if ($request->filled('search')) {
//             $buildings = $buildings->TextSearch(e($request->input('search')));
//         }

//         if ($request->filled('company_id')) {
//             $buildings->where('company_id','=',$request->input('company_id'));
//         }

//         // if ($request->filled('category_id')) {
//         //     $buildings->where('category_id','=',$request->input('category_id'));
//         // }

//         // if ($request->filled('manufacturer_id')) {
//         //     $buildings->where('manufacturer_id','=',$request->input('manufacturer_id'));
//         // }


//         // Set the offset to the API call's offset, unless the offset is higher than the actual count of items in which
//         // case we override with the actual count, so we should return 0 items.
//         $offset = (($buildings) && ($request->get('offset') > $buildings->count())) ? $buildings->count() : $request->get('offset', 0);

//         // Check to make sure the limit is not higher than the max allowed
//         ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit'))) ? $limit = $request->input('limit') : $limit = config('app.max_results');

//         $allowed_columns = ['id','name',
//             // 'order_number','min_amt','purchase_date',
//             'purchase_cost','company',
//             // 'category','model_number', 'item_no', 'manufacturer',
//             'location', 'area',
//             // 'qty','image'
//         ];
//         $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
//         $sort = in_array($request->input('sort'), $allowed_columns) ? $request->input('sort') : 'created_at';


//         switch ($sort) {
//             // case 'category':
//             //     $buildings = $buildings->OrderCategory($order);
//             //     break;
//             case 'location':
//                 $buildings = $buildings->OrderLocation($order);
//                 break;
//             // case 'manufacturer':
//             //     $buildings = $buildings->OrderManufacturer($order);
//             //     break;
//             case 'company':
//                 $buildings = $buildings->OrderCompany($order);
//                 break;
//             default:
//                 $buildings = $buildings->orderBy($sort, $order);
//                 break;
//         }



//         $total = $buildings->count();
//         $buildings = $buildings->skip($offset)->take($limit)->get();
//         return (new BuildingsTransformer)->transformBuildings($buildings, $total);

//     }


//     /**
//      * Store a newly created resource in storage.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @since [v4.0]
//      * @param  \Illuminate\Http\Request $request
//      * @return \Illuminate\Http\Response
//      */
//     public function store(Request $request)
//     {
//         $this->authorize('create', Building::class);
//         $building = new Building;
//         $building->fill($request->all());

//         if ($building->save()) {
//             return response()->json(Helper::formatStandardApiResponse('success', $building, trans('admin/buildings/message.create.success')));
//         }
//         return response()->json(Helper::formatStandardApiResponse('error', null, $building->getErrors()));
//     }

//     /**
//      * Display the specified resource.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @param  int $id
//      * @return \Illuminate\Http\Response
//      */
//     public function show($id)
//     {
//         $this->authorize('view', Building::class);
//         $building = Building::findOrFail($id);
//         return (new BuildingsTransformer)->transformBuilding($building);
//     }


//     /**
//      * Update the specified resource in storage.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @since [v4.0]
//      * @param  \Illuminate\Http\Request $request
//      * @param  int $id
//      * @return \Illuminate\Http\Response
//      */
//     public function update(Request $request, $id)
//     {
//         $this->authorize('update', Building::class);
//         $building = Building::findOrFail($id);
//         $building->fill($request->all());

//         if ($building->save()) {
//             return response()->json(Helper::formatStandardApiResponse('success', $building, trans('admin/buildings/message.update.success')));
//         }

//         return response()->json(Helper::formatStandardApiResponse('error', null, $building->getErrors()));
//     }

//     /**
//      * Remove the specified resource from storage.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @since [v4.0]
//      * @param  int $id
//      * @return \Illuminate\Http\Response
//      */
//     public function destroy($id)
//     {
//         $this->authorize('delete', Building::class);
//         $building = Building::findOrFail($id);
//         $this->authorize('delete', $building);
//         $building->delete();
//         return response()->json(Helper::formatStandardApiResponse('success', null,  trans('admin/buildings/message.delete.success')));
//     }

//     /**
//     * Returns a JSON response containing details on the users associated with this building.
//     *
//     * @author [A. Gianotto] [<snipe@snipe.net>]
//     * @see \App\Http\Controllers\Buildings\BuildingsController::getView() method that returns the form.
//     * @since [v1.0]
//     * @param int $buildingId
//     * @return array
//      */
//     public function getDataView($buildingId)
//     {
//         $building = Building::with(array('buildingAssignments'=>
//         function ($query) {
//             $query->orderBy($query->getModel()->getTable().'.created_at', 'DESC');
//         },
//         'buildingAssignments.admin'=> function ($query) {
//         },
//         'buildingAssignments.user'=> function ($query) {
//         },
//         ))->find($buildingId);

//         if (!Company::isCurrentUserHasAccess($building)) {
//             return ['total' => 0, 'rows' => []];
//         }
//         $this->authorize('view', Building::class);
//         $rows = array();

//         foreach ($building->buildingAssignments as $building_assignment) {
//             $rows[] = [
//                 'name' => ($building_assignment->user) ? $building_assignment->user->present()->nameUrl() : 'Deleted User',
//                 'created_at' => Helper::getFormattedDateObject($building_assignment->created_at, 'datetime'),
//                 'admin' => ($building_assignment->admin) ? $building_assignment->admin->present()->nameUrl() : '',
//             ];
//         }

//         $buildingCount = $building->users->count();
//         $data = array('total' => $buildingCount, 'rows' => $rows);
//         return $data;
//     }

//     /**
//      * Checkout a building
//      *
//      * @author [A. Gutierrez] [<andres@baller.tv>]
//      * @param int $id
//      * @since [v4.9.5]
//      * @return JsonResponse
//      */
//     public function checkout(Request $request, $id)
//     {
//         // Check if the building exists
//         if (is_null($building = Building::find($id))) {
//             return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/buildings/message.does_not_exist')));
//         }

//         $this->authorize('checkout', $building);

//         if ($building->qty > 0) {

//             // Check if the user exists
//             $assigned_to = $request->input('assigned_to');
//             if (is_null($user = User::find($assigned_to))) {
//                 // Return error message
//                 return response()->json(Helper::formatStandardApiResponse('error', null, 'No user found'));
//             }

//             // Update the building data
//             $building->assigned_to = e($assigned_to);

//             $building->users()->attach($building->id, [
//                 'building_id' => $building->id,
//                 'user_id' => $user->id,
//                 'assigned_to' => $assigned_to
//             ]);

//             // Log checkout event
//             $logaction = $building->logCheckout(e($request->input('note')), $user);
//             $data['log_id'] = $logaction->id;
//             $data['eula'] = $building->getEula();
//             $data['first_name'] = $user->first_name;
//             $data['item_name'] = $building->name;
//             $data['checkout_date'] = $logaction->created_at;
//             $data['note'] = $logaction->note;
//             $data['require_acceptance'] = $building->requireAcceptance();

//             return response()->json(Helper::formatStandardApiResponse('success', null,  trans('admin/buildings/message.checkout.success')));
//         }

//         return response()->json(Helper::formatStandardApiResponse('error', null, 'No buildings remaining'));
//     }

//     /**
//     * Gets a paginated collection for the select2 menus
//     *
//     * @see \App\Http\Transformers\SelectlistTransformer
//     *
//     */
//     public function selectlist(Request $request)
//     {

//         $buildings = Building::select([
//             'buildings.id',
//             'buildings.name'
//         ]);

//         if ($request->filled('search')) {
//             $buildings = $buildings->where('buildings.name', 'LIKE', '%'.$request->get('search').'%');
//         }

//         $buildings = $buildings->orderBy('name', 'ASC')->paginate(50);


//         return (new SelectlistTransformer)->transformSelectlist($buildings);
//     }
// }
