<?php

// namespace App\Http\Controllers\Api;

// use App\Helpers\Helper;
// use App\Http\Controllers\Controller;
// use App\Http\Transformers\LandTransformer;
// use App\Http\Transformers\SelectlistTransformer;
// use App\Models\Company;
// use App\Models\Land;
// use App\Models\User;
// use Illuminate\Http\Request;

// class LandController extends Controller
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
//         $this->authorize('index', Land::class);
//         $land = Company::scopeCompanyables(
//             Land::select('lands.*')
//                 ->with('company', 'location',
//                 // 'area',
//                 // 'category',
//                 'users',
//                 // 'manufacturer'
//                 )
//         );

//         if ($request->filled('search')) {
//             $land = $land->TextSearch(e($request->input('search')));
//         }

//         if ($request->filled('company_id')) {
//             $land->where('company_id','=',$request->input('company_id'));
//         }

//         // if ($request->filled('category_id')) {
//         //     $land->where('category_id','=',$request->input('category_id'));
//         // }

//         // if ($request->filled('manufacturer_id')) {
//         //     $land->where('manufacturer_id','=',$request->input('manufacturer_id'));
//         // }


//         // Set the offset to the API call's offset, unless the offset is higher than the actual count of items in which
//         // case we override with the actual count, so we should return 0 items.
//         $offset = (($land) && ($request->get('offset') > $land->count())) ? $land->count() : $request->get('offset', 0);

//         // Check to make sure the limit is not higher than the max allowed
//         ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit'))) ? $limit = $request->input('limit') : $limit = config('app.max_results');

//         $allowed_columns = ['id','name',
//             // 'order_number','min_amt','purchase_date','purchase_cost',
//             'company',
//             // 'category','model_number', 'item_no', 'manufacturer',
//             'location',
//             'area',
//             // 'qty', 'image'
//         ];
//         $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
//         $sort = in_array($request->input('sort'), $allowed_columns) ? $request->input('sort') : 'created_at';


//         switch ($sort) {
//             // case 'category':
//             //     $land = $land->OrderCategory($order);
//             //     break;
//             case 'location':
//                 $land = $land->OrderLocation($order);
//                 break;
//             // case 'manufacturer':
//             //     $land = $land->OrderManufacturer($order);
//             //     break;
//             case 'company':
//                 $land = $land->OrderCompany($order);
//                 break;
//             default:
//                 $land = $land->orderBy($sort, $order);
//                 break;
//         }



//         $total = $land->count();
//         $land = $land->skip($offset)->take($limit)->get();
//         return (new LandTransformer)->transformLands($land, $total);

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
//         $this->authorize('create', Land::class);
//         $land = new Land;
//         $land->fill($request->all());

//         if ($land->save()) {
//             return response()->json(Helper::formatStandardApiResponse('success', $land, trans('admin/land/message.create.success')));
//         }
//         return response()->json(Helper::formatStandardApiResponse('error', null, $land->getErrors()));
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
//         $this->authorize('view', Land::class);
//         $land = Land::findOrFail($id);
//         return (new LandTransformer)->transformLand($land);
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
//         $this->authorize('update', Land::class);
//         $land = Land::findOrFail($id);
//         $land->fill($request->all());

//         if ($land->save()) {
//             return response()->json(Helper::formatStandardApiResponse('success', $land, trans('admin/land/message.update.success')));
//         }

//         return response()->json(Helper::formatStandardApiResponse('error', null, $land->getErrors()));
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
//         $this->authorize('delete', Land::class);
//         $land = Land::findOrFail($id);
//         $this->authorize('delete', $land);
//         $land->delete();
//         return response()->json(Helper::formatStandardApiResponse('success', null,  trans('admin/land/message.delete.success')));
//     }

//     /**
//     * Returns a JSON response containing details on the users associated with this land.
//     *
//     * @author [A. Gianotto] [<snipe@snipe.net>]
//     * @see \App\Http\Controllers\Land\LandController::getView() method that returns the form.
//     * @since [v1.0]
//     * @param int $landId
//     * @return array
//      */
//     public function getDataView($landId)
//     {
//         $land = Land::with(array('landAssignments'=>
//         function ($query) {
//             $query->orderBy($query->getModel()->getTable().'.created_at', 'DESC');
//         },
//         'landAssignments.admin'=> function ($query) {
//         },
//         'landAssignments.user'=> function ($query) {
//         },
//         ))->find($landId);

//         if (!Company::isCurrentUserHasAccess($land)) {
//             return ['total' => 0, 'rows' => []];
//         }
//         $this->authorize('view', Land::class);
//         $rows = array();

//         foreach ($land->landAssignments as $land_assignment) {
//             $rows[] = [
//                 'name' => ($land_assignment->user) ? $land_assignment->user->present()->nameUrl() : 'Deleted User',
//                 'created_at' => Helper::getFormattedDateObject($land_assignment->created_at, 'datetime'),
//                 'admin' => ($land_assignment->admin) ? $land_assignment->admin->present()->nameUrl() : '',
//             ];
//         }

//         $landCount = $land->users->count();
//         $data = array('total' => $landCount, 'rows' => $rows);
//         return $data;
//     }

//     /**
//      * Checkout a land
//      *
//      * @author [A. Gutierrez] [<andres@baller.tv>]
//      * @param int $id
//      * @since [v4.9.5]
//      * @return JsonResponse
//      */
//     public function checkout(Request $request, $id)
//     {
//         // Check if the land exists
//         if (is_null($land = Land::find($id))) {
//             return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/land/message.does_not_exist')));
//         }

//         $this->authorize('checkout', $land);

//         if ($land->qty > 0) {

//             // Check if the user exists
//             $assigned_to = $request->input('assigned_to');
//             if (is_null($user = User::find($assigned_to))) {
//                 // Return error message
//                 return response()->json(Helper::formatStandardApiResponse('error', null, 'No user found'));
//             }

//             // Update the land data
//             $land->assigned_to = e($assigned_to);

//             $land->users()->attach($land->id, [
//                 'land_id' => $land->id,
//                 'user_id' => $user->id,
//                 'assigned_to' => $assigned_to
//             ]);

//             // Log checkout event
//             $logaction = $land->logCheckout(e($request->input('note')), $user);
//             $data['log_id'] = $logaction->id;
//             $data['eula'] = $land->getEula();
//             $data['first_name'] = $user->first_name;
//             $data['item_name'] = $land->name;
//             $data['checkout_date'] = $logaction->created_at;
//             $data['note'] = $logaction->note;
//             $data['require_acceptance'] = $land->requireAcceptance();

//             return response()->json(Helper::formatStandardApiResponse('success', null,  trans('admin/land/message.checkout.success')));
//         }

//         return response()->json(Helper::formatStandardApiResponse('error', null, 'No land remaining'));
//     }

//     /**
//     * Gets a paginated collection for the select2 menus
//     *
//     * @see \App\Http\Transformers\SelectlistTransformer
//     *
//     */
//     public function selectlist(Request $request)
//     {

//         $land = Land::select([
//             'lands.id',
//             'lands.name'
//         ]);

//         if ($request->filled('search')) {
//             $land = $land->where('lands.name', 'LIKE', '%'.$request->get('search').'%');
//         }

//         $land = $land->orderBy('name', 'ASC')->paginate(50);


//         return (new SelectlistTransformer)->transformSelectlist($land);
//     }
// }
