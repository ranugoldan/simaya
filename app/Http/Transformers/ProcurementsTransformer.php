<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Procurement;
use Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcurementsTransformer
{
  public function transformProcurements(Collection $procurements, $total)
  {
    $array = array();
    foreach ($procurements as $procurement) {
      $array[] = self::transformProcurement($procurement);
    }
    return (new DatatablesTransformer)->transformDatatables($array, $total);
  }

  public function transformProcurement(Procurement $procurement = null)
  {
    if ($procurement) {
      $models_arr = [];
      $assets_arr = [];
      $locations_arr = [];

      if (!is_null($procurement->models)) {
        foreach($procurement->models as $model) {
          $models_arr[] = [
            'id' => (int) $model->id,
            'name' => $model->name
          ];
        }
      }

      if (!is_null($procurement->assets)) {
        foreach($procurement->assets as $asset) {
          $assets_arr[] = [
            'id' => (int) $asset->id,
            'name' => $asset->name
          ];
        }
      }

      if (!is_null($procurement->locations)) {
        foreach($procurement->locations as $location) {
          $locations_arr[] = [
            'id' => (int) $location->id,
            'name' => $location->name
          ];
        }
      }

      $array = [
        'id'              => (int) $procurement->id,
        'procurement_tag' => ($procurement->procurement_tag) ? e($procurement->procurement_tag) : null,
        'status'          => ($procurement->status) ? Helper::formatProcurementStatus($procurement->status) : null,
        'models'          => $models_arr,
        'assets'          => $assets_arr,
        'supplier'        => ($procurement->supplier) ? [
                                                          'id'    => (int) $procurement->supplier->id,
                                                          'name'  => e($procurement->supplier->name)
                                                        ] : null,
        'qty'             => ($procurement->models->isNotEmpty()) ? (int) $procurement->models[0]->pivot->qty : null,
        'purchase_cost'   => ($procurement->models->isNotEmpty()) ? Helper::formatCurrencyOutput($procurement->models[0]->pivot->purchase_cost) : null,
        'locations'       => $locations_arr,
        'department'      => ($procurement->department) ? [
                                                            'id'    => (int) $procurement->department->id,
                                                            'name'  => e($procurement->department->name)
                                                          ] : null,
        'user'            => ($procurement->user) ? (new UsersTransformer)->transformUser($procurement->user) : null,
        'created_at'      => Helper::getFormattedDateObject($procurement->created_at, 'datetime'),
      ];

      $permissions_array['available_actions'] = [
        'approve' => $procurement->isApprovable(),
        'assign'  => $procurement->isAssignable(),
        'update'  => Gate::allows('update', Procurement::class) ? true : false,
        'delete'  => $procurement->isDeletable(),
      ];

      $array += $permissions_array;

      return $array;
    }
  }
}