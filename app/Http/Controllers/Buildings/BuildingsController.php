<?php

// namespace App\Http\Controllers\Buildings;

// use App\Helpers\Helper;
// use App\Http\Controllers\Controller;
// use App\Http\Requests\ImageUploadRequest;
// use App\Models\Company;
// use App\Models\Building;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Input;

// /**
//  * This controller handles all actions related to Buildings for
//  * the Snipe-IT Asset Management application.
//  *
//  * @version    v1.0
//  */
// class BuildingsController extends Controller
// {
//     /**
//      * Return a view to display component information.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @see BuildingsController::getDatatable() method that generates the JSON response
//      * @since [v1.0]
//      * @return \Illuminate\Contracts\View\View
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function index()
//     {
//         $this->authorize('index', Building::class);
//         return view('buildings/index');
//     }


//     /**
//      * Return a view to display the form view to create a new building
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @see BuildingsController::postCreate() method that stores the form data
//      * @since [v1.0]
//      * @return \Illuminate\Contracts\View\View
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function create()
//     {
//         $this->authorize('create', Building::class);
//         return view('buildings/edit')->with('category_type', 'building')
//             ->with('item', new Building);
//     }


//     /**
//      * Validate and store new building data.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @see BuildingsController::getCreate() method that returns the form view
//      * @since [v1.0]
//      * @param ImageUploadRequest $request
//      * @return \Illuminate\Http\RedirectResponse
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function store(ImageUploadRequest $request)
//     {
//         $this->authorize('create', Building::class);
//         $building = new Building();
//         $building->name                   = $request->input('name');
//         // $building->category_id            = $request->input('category_id');
//         $building->location_id            = $request->input('location_id');
//         $building->area                   = $request->input('area');
//         $building->company_id             = Company::getIdForCurrentUser($request->input('company_id'));
//         // $building->order_number           = $request->input('order_number');
//         // $building->min_amt                = $request->input('min_amt');
//         // $building->manufacturer_id        = $request->input('manufacturer_id');
//         // $building->model_number           = $request->input('model_number');
//         // $building->item_no                = $request->input('item_no');
//         // $building->purchase_date          = $request->input('purchase_date');
//         $building->purchase_cost          = Helper::ParseFloat($request->input('purchase_cost'));
//         // $building->qty                    = $request->input('qty');
//         $building->user_id                = Auth::id();


//         $building = $request->handleImages($building);

//         if ($building->save()) {
//             return redirect()->route('buildings.index')->with('success', trans('admin/buildings/message.create.success'));
//         }

//         return redirect()->back()->withInput()->withErrors($building->getErrors());

//     }

//     /**
//      * Returns a form view to edit a building.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @param  int $buildingId
//      * @see BuildingsController::postEdit() method that stores the form data.
//      * @since [v1.0]
//      * @return \Illuminate\Contracts\View\View
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function edit($buildingId = null)
//     {
//         if ($item = Building::find($buildingId)) {
//             $this->authorize($item);
//             return view('buildings/edit', compact('item'))->with('category_type', 'building');
//         }

//         return redirect()->route('buildings.index')->with('error', trans('admin/buildings/message.does_not_exist'));

//     }


//     /**
//      * Returns a form view to edit a building.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @param ImageUploadRequest $request
//      * @param  int $buildingId
//      * @return \Illuminate\Http\RedirectResponse
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      * @see BuildingsController::getEdit() method that stores the form data.
//      * @since [v1.0]
//      */
//     public function update(ImageUploadRequest $request, $buildingId = null)
//     {
//         if (is_null($building = Building::find($buildingId))) {
//             return redirect()->route('buildings.index')->with('error', trans('admin/buildings/message.does_not_exist'));
//         }

//         $this->authorize($building);

//         $building->name                   = $request->input('name');
//         // $building->category_id            = $request->input('category_id');
//         $building->location_id            = $request->input('location_id');
//         $building->area                   = $request->input('area');
//         $building->company_id             = Company::getIdForCurrentUser($request->input('company_id'));
//         // $building->order_number           = $request->input('order_number');
//         // $building->min_amt                = $request->input('min_amt');
//         // $building->manufacturer_id        = $request->input('manufacturer_id');
//         // $building->model_number           = $request->input('model_number');
//         // $building->item_no                = $request->input('item_no');
//         // $building->purchase_date          = $request->input('purchase_date');
//         $building->purchase_cost          = Helper::ParseFloat($request->input('purchase_cost'));
//         // $building->qty                    = Helper::ParseFloat($request->input('qty'));

//         $building = $request->handleImages($building);

//         if ($building->save()) {
//             return redirect()->route('buildings.index')->with('success', trans('admin/buildings/message.update.success'));
//         }
//         return redirect()->back()->withInput()->withErrors($building->getErrors());
//     }

//     /**
//      * Delete a building.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @param  int $buildingId
//      * @since [v1.0]
//      * @return \Illuminate\Http\RedirectResponse
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function destroy($buildingId)
//     {
//         if (is_null($building = Building::find($buildingId))) {
//             return redirect()->route('buildings.index')->with('error', trans('admin/buildings/message.not_found'));
//         }
//         $this->authorize($building);
//         $building->delete();
//         // Redirect to the buildings management page
//         return redirect()->route('buildings.index')->with('success', trans('admin/buildings/message.delete.success'));
//     }

//     /**
//      * Return a view to display component information.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @see BuildingsController::getDataView() method that generates the JSON response
//      * @since [v1.0]
//      * @param int $buildingId
//      * @return \Illuminate\Contracts\View\View
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function show($buildingId = null)
//     {
//         $building = Building::find($buildingId);
//         $this->authorize($building);
//         if (isset($building->id)) {
//             return view('buildings/view', compact('building'));
//         }
//         return redirect()->route('buildings.index')
//             ->with('error', trans('admin/buildings/message.does_not_exist'));
//     }

// }
