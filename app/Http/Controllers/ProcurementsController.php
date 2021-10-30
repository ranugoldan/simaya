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
        $this->authorize('create', Procurement::class);
        $procurement = new Procurement();
        $procurement->id                = null;
        $procurement->procurement_tag   = $request->input('procurement_tag');
        $procurement->status            = 1;
        $procurement->supplier_id       = $request->input('supplier_id');
        $procurement->department_id     = $request->input('department_id');
        $procurement->user_id           = $request->input('user_id');
        
        $model_ids                      = $request->input('model_id');
        $model_qty                      = $request->input('qty');
        $model_purchase_cost            = $request->input('purchase_cost');

        $model_payload                  = array();
        
        foreach($model_ids as $idx => $model_id) {
            $model_payload[$model_id] = [
                'qty' => $model_qty[$idx],
                'purchase_cost' => $model_purchase_cost[$idx]
            ];
        }

        $location_id                    = $request->input('location_id');

        $procurement = $request->handleImages($procurement);

        if ($procurement->save()) {
            $procurement->models()->sync($model_payload);
            $procurement->locations()->sync($location_id);
            
            return redirect()->route("procurements.index")->with('success', trans('admin/procurements/message.create.success'));
        }

        return redirect()->back()->withInput()->withErrors($procurement->getErrors());
    }

    public function edit($procurementId = null)
    {
        $this->authorize('update', Procurement::class);

        if (is_null($item = Procurement::find($procurementId))) {
            return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.does_not_exist'));
        }

        return view('procurements/edit', compact('item'));
    }

    public function update(ImageUploadRequest $request, $procurementId = null)
    {
        $this->authorize('update', Procurement::class);

        if (is_null($procurement = Procurement::find($procurementId))) {
            return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.does_not_exist'));
        }

        $procurement->procurement_tag   = $request->input('procurement_tag');
        $procurement->status            = $procurement->status;
        $procurement->supplier_id       = $request->input('supplier_id');
        $procurement->department_id     = $request->input('department_id');
        $procurement->user_id           = $request->input('user_id');
        
        $model_ids                      = $request->input('model_id');
        $model_qty                      = $request->input('qty');
        $model_purchase_cost            = $request->input('purchase_cost');

        $model_payload                  = array();
        
        foreach($model_ids as $idx => $model_id) {
            $model_payload[$model_id] = [
                'qty' => $model_qty[$idx],
                'purchase_cost' => $model_purchase_cost[$idx]
            ];
        }

        $location_id                    = $request->input('location_id');

        $procurement = $request->handleImages($procurement);

        if ($procurement->save()) {
            $procurement->models()->sync($model_payload);
            $procurement->locations()->sync($location_id);
            
            return redirect()->route("procurements.index")->with('success', trans('admin/procurements/message.update.success'));
        }

        return redirect()->back()->withInput()->withErrors($procurement->getErrors());
    }

    public function destroy($procurementId)
    {
        $this->authorize('delete', Procurement::class);
        if (is_null($procurement = Procurement::find($procurementId))) {
            return redirect()->to(route('procurements.index'))->with('error', trans('admin/procurements/message.does_not_exist'));
        }

        $procurement->delete();

        return redirect()->to(route('procurements.index'))->with('success', trans('admin/procurements/message.delete.success'));
    }

    public function show($procurementId = null)
    {
        $procurement = Procurement::find($procurementId);

        if (isset($procurement->id)) {
            return view('procurements/view', compact('procurement'));
        }

        return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.does_not_exist'));
    }

    public function view_approve($procurementId = null)
    {
        $this->authorize('approve', Procurement::class);

        $procurement = Procurement::find($procurementId);

        if (isset($procurement->id)) {
            return view('procurements/approve', compact('procurement'));
        }

        return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.does_not_exist'));
    }

    public function update_approve(ImageUploadRequest $request, $procurementId = null)
    {
        $this->authorize('approve', Procurement::class);
        
        if (is_null($procurement = Procurement::find($procurementId))) {
            return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.does_not_exist'));
        }

        $procurement = $request->handleImages($procurement, 600, 'procurement_form', 'procurement_form', 'procurement_form');

        if (isset($procurement->procurement_form)) {
            $procurement->status = 2;
        }

        if ($procurement->save()) {
            return redirect()->route("procurements.index")->with('success', trans('admin/procurements/message.approve.success'));
        }

        return redirect()->back()->withInput()->withErrors($procurement->getErrors());
    }

    public function view_assign($procurementId = null)
    {
        $this->authorize('assign', Procurement::class);

        $procurement = Procurement::find($procurementId);

        if (isset($procurement->id)) {
            return view('procurements/assign', compact('procurement'));
        }

        return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.does_not_exist'));
    }

    public function update_assign(ImageUploadRequest $request, $procurementId = null)
    {
        $this->authorize('assign', Procurement::class);
        
        if (is_null($procurement = Procurement::find($procurementId))) {
            return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.does_not_exist'));
        }

        $asset_ids = collect($request->asset_id);

        if (($procurement->models->count()) == $asset_ids->count()) {
            $procurement->status = 3;
        }

        if ($procurement->save()) {
            $procurement->assets()->sync($asset_ids);

            return redirect()->route("procurements.index")->with('success', trans('admin/procurements/message.assign.success'));
        }

        return redirect()->back()->withInput()->withErrors($procurement->getErrors());
    }
}
