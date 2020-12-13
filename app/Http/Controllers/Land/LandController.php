<?php

// namespace App\Http\Controllers\Land;

// use App\Helpers\Helper;
// use App\Http\Controllers\Controller;
// use App\Http\Requests\ImageUploadRequest;
// use App\Models\Company;
// use App\Models\Land;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Input;

// /**
//  * This controller handles all actions related to Land for
//  * the Snipe-IT Asset Management application.
//  *
//  * @version    v1.0
//  */
// class LandController extends Controller
// {
//     /**
//      * Return a view to display land information.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @see LandController::getDatatable() method that generates the JSON response
//      * @since [v1.0]
//      * @return \Illuminate\Contracts\View\View
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function index()
//     {
//         $this->authorize('index', Land::class);
//         return view('land/index');
//     }


//     /**
//      * Return a view to display the form view to create a new land
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @see LandController::postCreate() method that stores the form data
//      * @since [v1.0]
//      * @return \Illuminate\Contracts\View\View
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function create()
//     {
//         $this->authorize('create', Land::class);
//         return view('land/edit')->with('category_type', 'land')
//             ->with('item', new Land);
//     }


//     /**
//      * Validate and store new land data.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @see LandController::getCreate() method that returns the form view
//      * @since [v1.0]
//      * @param ImageUploadRequest $request
//      * @return \Illuminate\Http\RedirectResponse
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function store(ImageUploadRequest $request)
//     {
//         $this->authorize('create', Land::class);
//         $land = new Land();
//         $land->name                   = $request->input('name');
//         // $land->category_id            = $request->input('category_id');
//         $land->location_id            = $request->input('location_id');
//         $land->area                   = $request->input('area');
//         $land->company_id             = Company::getIdForCurrentUser($request->input('company_id'));
//         // $land->order_number           = $request->input('order_number');
//         // $land->min_amt                = $request->input('min_amt');
//         // $land->manufacturer_id        = $request->input('manufacturer_id');
//         // $land->model_number           = $request->input('model_number');
//         // $land->item_no                = $request->input('item_no');
//         // $land->purchase_date          = $request->input('purchase_date');
//         // $land->purchase_cost          = Helper::ParseFloat($request->input('purchase_cost'));
//         // $land->qty                    = $request->input('qty');
//         $land->user_id                = Auth::id();


//         $land = $request->handleImages($land);

//         if ($land->save()) {
//             return redirect()->route('land.index')->with('success', trans('admin/land/message.create.success'));
//         }

//         return redirect()->back()->withInput()->withErrors($land->getErrors());

//     }

//     /**
//      * Returns a form view to edit a land.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @param  int $landId
//      * @see LandController::postEdit() method that stores the form data.
//      * @since [v1.0]
//      * @return \Illuminate\Contracts\View\View
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function edit($landId = null)
//     {
//         if ($item = Land::find($landId)) {
//             $this->authorize($item);
//             return view('land/edit', compact('item'))->with('category_type', 'land');
//         }

//         return redirect()->route('land.index')->with('error', trans('admin/land/message.does_not_exist'));

//     }


//     /**
//      * Returns a form view to edit a land.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @param ImageUploadRequest $request
//      * @param  int $landId
//      * @return \Illuminate\Http\RedirectResponse
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      * @see LandController::getEdit() method that stores the form data.
//      * @since [v1.0]
//      */
//     public function update(ImageUploadRequest $request, $landId = null)
//     {
//         if (is_null($land = Land::find($landId))) {
//             return redirect()->route('land.index')->with('error', trans('admin/land/message.does_not_exist'));
//         }

//         $this->authorize($land);

//         $land->name                   = $request->input('name');
//         // $land->category_id            = $request->input('category_id');
//         $land->location_id            = $request->input('location_id');
//         $land->area                   = $request->input('area');
//         $land->company_id             = Company::getIdForCurrentUser($request->input('company_id'));
//         // $land->order_number           = $request->input('order_number');
//         // $land->min_amt                = $request->input('min_amt');
//         // $land->manufacturer_id        = $request->input('manufacturer_id');
//         // $land->model_number           = $request->input('model_number');
//         // $land->item_no                = $request->input('item_no');
//         // $land->purchase_date          = $request->input('purchase_date');
//         // $land->purchase_cost          = Helper::ParseFloat($request->input('purchase_cost'));
//         // $land->qty                    = Helper::ParseFloat($request->input('qty'));

//         $land = $request->handleImages($land);

//         if ($land->save()) {
//             return redirect()->route('land.index')->with('success', trans('admin/land/message.update.success'));
//         }
//         return redirect()->back()->withInput()->withErrors($land->getErrors());
//     }

//     /**
//      * Delete a land.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @param  int $landId
//      * @since [v1.0]
//      * @return \Illuminate\Http\RedirectResponse
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function destroy($landId)
//     {
//         if (is_null($land = Land::find($landId))) {
//             return redirect()->route('land.index')->with('error', trans('admin/land/message.not_found'));
//         }
//         $this->authorize($land);
//         $land->delete();
//         // Redirect to the land management page
//         return redirect()->route('land.index')->with('success', trans('admin/land/message.delete.success'));
//     }

//     /**
//      * Return a view to display land information.
//      *
//      * @author [A. Gianotto] [<snipe@snipe.net>]
//      * @see LandController::getDataView() method that generates the JSON response
//      * @since [v1.0]
//      * @param int $landId
//      * @return \Illuminate\Contracts\View\View
//      * @throws \Illuminate\Auth\Access\AuthorizationException
//      */
//     public function show($landId = null)
//     {
//         $land = Land::find($landId);
//         $this->authorize($land);
//         if (isset($land->id)) {
//             return view('land/view', compact('land'));
//         }
//         return redirect()->route('land.index')
//             ->with('error', trans('admin/land/message.does_not_exist'));
//     }

// }
