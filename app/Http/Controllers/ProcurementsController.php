<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ImageUploadRequest;
use App\Models\Procurement;
use Carbon\Carbon;
use PdfReport;

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

        if ($request->has('asset_id')) {
            $asset_id                   = $request->input('asset_id');
        }

        if ($request->has('procurement_form_delete') && $request->filled('procurement_form_delete')) {
            try {
                unlink(public_path().'/uploads/procurement_form/'.$procurement->procurement_form);
                $procurement->procurement_form = NULL;
            } catch (\Exception $e) {
                \Log::info($e);
            }
        }

        $procurement = $request->handleImages($procurement, 1200, 'procurement_form', 'procurement_form', 'procurement_form');

        if ($procurement->save()) {
            $procurement->models()->sync($model_payload);
            $procurement->locations()->sync($location_id);
            if (isset($asset_id)) {
                $procurement->assets()->sync($asset_id);
            }
            
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

        if ($procurement->procurement_form) {
            try {
                Storage::disk('public')->delete('procurement_form/'.$procurement->procurement_form);
            } catch (\Exception $e) {
                \Log::error($e);
            }
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

        if (isset($procurement->procurement_form)) {
            return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.approval_uploaded'));
        }

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

        $procurement = $request->handleImages($procurement, 1200, 'procurement_form', 'procurement_form', 'procurement_form');

        if (isset($procurement->procurement_form) && $procurement->status != 3) {
            $procurement->status = 2;
            $procurement->approved_by = Auth::id();
            $procurement->approved_at = Carbon::now()->toDateTimeString();
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

        if (is_null($procurement->procurement_form)) {
            return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.approval_not_uploaded'));
        }

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

        if ((($procurement->models->count()) == $asset_ids->count()) && isset($procurement->procurement_form)) {
            $procurement->status = 3;
            $procurement->assigned_by = Auth::id();
            $procurement->assigned_at = Carbon::now()->toDateTimeString();
        }

        if ($procurement->save()) {
            $procurement->assets()->sync($asset_ids);

            return redirect()->route("procurements.index")->with('success', trans('admin/procurements/message.assign.success'));
        }

        return redirect()->back()->withInput()->withErrors($procurement->getErrors());
    }

    public function print($procurementId = null)
    {
        $this->authorize('view', Procurement::class);

        if (is_null($procurement = Procurement::find($procurementId))) {
            return redirect()->route('procurements.index')->with('error', trans('admin/procurements/message.does_not_exist'));
        }

        $locations = $procurement->locations;
        $locations_str = '';

        foreach ($locations as $idx => $location) {
            if ($idx == 0) {
                $locations_str .= $location->name;
            } else {
                $locations_str .= ', '.$location->name;
            }
        }

        $title = 'Pengadaan '.$procurement->procurement_tag;

        $meta = [
            'No. Pengajuan'     => $procurement->procurement_tag,
            'Tanggal Pengajuan' => $procurement->created_at,
            'Vendor'            => $procurement->supplier->name,
            'Unit/Bidang'       => $procurement->department->name,
            'Posisi'            => $locations_str,
        ];

        $query = DB::table('procurement_models')
                    ->join('procurements', 'procurement_models.procurement_id', '=', 'procurements.id')
                    ->join('models', 'procurement_models.model_id', '=', 'models.id')
                    ->join('users', 'procurements.user_id', '=', 'users.id')
                    ->select('procurement_models.*', 'models.name', 'users.first_name', 'users.last_name', 'users.jobtitle')
                    ->where('procurement_models.procurement_id', '=', $procurementId);

        $columns = [
            'Nama Barang' => function($result) {
                return $result->name;
            },
            'qty',
            'Harga' => function($result) {
                return \App\Helpers\Helper::formatCurrencyOutput($result->purchase_cost);
            },
            'Jumlah' => function($result) {
                return \App\Helpers\Helper::formatCurrencyOutput($result->qty * $result->purchase_cost);
            }
        ];

        return PdfReport::of($title, $meta, $query, $columns)
                        ->editColumns(['Harga', 'Jumlah'], ['class' => 'right'])
                        ->showTotal(['Jumlah' => 'point'])
                        ->download($title);
    }
}
