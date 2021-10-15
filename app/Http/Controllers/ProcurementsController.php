<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImageUploadRequest;
use App\Models\Procurement;

class ProcurementsController extends Controller
{
    public function index()
    {
        $this->authorize('view', Procurement::class);
        return view('procurements/index');
    }

    public function create()
    {
        $this->authorize('create', Procurement::class);
        return view('procurements/edit')
            ->with('item', new Procurement);
    }

    public function store(ImageUploadRequest $request)
    {
        // dd($request);
        $this->authorize('create', Procurement::class);
        $procurement = new Procurement();
        $procurement->id                = null;
        $procurement->procurement_tag   = $request->input('procurement_tag');
        $procurement->status            = $request->input('status');
        // $procurement->model_id          = $request->input('model_id');
        // $procurement->asset_id          = $request->input('asset_id');
        $procurement->supplier_id       = $request->input('supplier_id');
        // $procurement->qty               = $request->input('qty');
        // $procurement->purchase_cost     = $request->input('purchase_cost');
        // $procurement->location_id       = $request->input('location_id');
        $procurement->department_id     = $request->input('department_id');
        $procurement->user_id           = $request->input('user_id');
        
        $model_id                       = $request->input('model_id');
        $model_qty                      = $request->input('qty');
        $model_purchase_cost            = $request->input('purchase_cost');

        $asset_id                       = $request->input('asset_id');

        $location_id                    = $request->input('location_id');

        $procurement = $request->handleImages($procurement);

        if ($procurement->save()) {
            $procurement->models()->attach($model_id, [
                'qty' => $model_qty,
                'purchase_cost' => $model_purchase_cost
            ]);
            $procurement->assets()->attach($asset_id);
            $procurement->locations()->attach($location_id);
            
            return redirect()->route("procurements.index")->with('success', trans('admin/procurements/message.create.success'));
        }

        dd($procurement->getErrors());
        return redirect()->back()->withInput()->withErrors($procurement->getErrors());
    }
}
