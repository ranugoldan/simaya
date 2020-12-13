<?php
namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Building;
use Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class BuildingsTransformer
{

    public function transformBuildings (Collection $buildings, $total)
    {
        $array = array();
        foreach ($buildings as $building) {
            $array[] = self::transformBuilding($building);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformBuilding (Building $building)
    {
        $array = [
            'id'            => (int) $building->id,
            'name'          => e($building->name),
            // 'image' =>   ($building->image) ? Storage::disk('public')->url('buildings/'.e($building->image)) : null,
            // 'category'      => ($building->category) ? ['id' => $building->category->id, 'name' => e($building->category->name)] : null,
            'company'   => ($building->company) ? ['id' => (int) $building->company->id, 'name' => e($building->company->name)] : null,
            // 'item_no'       => e($building->item_no),
            'location'      => ($building->location) ? ['id' => (int) $building->location->id, 'name' => e($building->location->name)] : null,
            // 'manufacturer'  => ($building->manufacturer) ? ['id' => (int) $building->manufacturer->id, 'name' => e($building->manufacturer->name)] : null,
            // 'min_amt'       => (int) $building->min_amt,
            // 'model_number'  => ($building->model_number!='') ? e($building->model_number) : null,
            // 'remaining'  => $building->numRemaining(),
            // 'order_number'  => e($building->order_number),
            'purchase_cost'  => Helper::formatCurrencyOutput($building->purchase_cost),
            // 'purchase_date'  => Helper::getFormattedDateObject($building->purchase_date, 'date'),
            // 'qty'           => (int) $building->qty,
            'created_at' => Helper::getFormattedDateObject($building->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($building->updated_at, 'datetime'),
        ];

        $permissions_array['user_can_checkout'] = false;

        if ($building->numRemaining() > 0) {
            $permissions_array['user_can_checkout'] = true;
        }

        $permissions_array['available_actions'] = [
            'checkout' => Gate::allows('checkout', Building::class),
            'checkin' => Gate::allows('checkin', Building::class),
            'update' => Gate::allows('update', Building::class),
            'delete' => Gate::allows('delete', Building::class),
        ];
        $array += $permissions_array;
        return $array;
    }


    public function transformCheckedoutBuildings (Collection $buildings_users, $total)
    {

        $array = array();
        foreach ($buildings_users as $user) {
            $array[] = (new UsersTransformer)->transformUser($user);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }



}
