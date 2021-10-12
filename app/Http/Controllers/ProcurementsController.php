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
        $procurement->status            = $request->input('status');
        $procurement->model_id          = $request->input('model_id');
        $procurement->asset_id          = $request->input('asset_id');
        $procurement->supplier_id       = $request->input('supplier_id');
        $procurement->qty               = $request->input('qty');
        $procurement->purchase_cost     = $request->input('purchase_cost');
        $procurement->location_id       = $request->input('location_id');
        $procurement->department_id     = $request->input('department_id');
        $procurement->user_id           = $request->input('user_id');

        if ($procurement->save()) {
            return redirect()->route("procurements.index")->with('success', trans('admin/procurements/message.create.success'));
        }

        return redirect()->back()->withInput()->withErrors($procurement->getErrors());
    }
}
