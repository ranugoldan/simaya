<?php
namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Land;
use Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class LandTransformer
{

    public function transformLands (Collection $lands, $total)
    {
        $array = array();
        foreach ($lands as $land) {
            $array[] = self::transformLand($land);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformLand (Land $land)
    {
        $array = [
            'id'            => (int) $land->id,
            'name'          => e($land->name),
            // 'image' =>   ($land->image) ? Storage::disk('public')->url('land/'.e($land->image)) : null,
            // 'category'      => ($land->category) ? ['id' => $land->category->id, 'name' => e($land->category->name)] : null,
            'company'   => ($land->company) ? ['id' => (int) $land->company->id, 'name' => e($land->company->name)] : null,
            // 'item_no'       => e($land->item_no),
            'location'      => ($land->location) ? ['id' => (int) $land->location->id, 'name' => e($land->location->name)] : null,
            'area'          => e($land->area),
            // 'manufacturer'  => ($land->manufacturer) ? ['id' => (int) $land->manufacturer->id, 'name' => e($land->manufacturer->name)] : null,
            // 'min_amt'       => (int) $land->min_amt,
            // 'model_number'  => ($land->model_number!='') ? e($land->model_number) : null,
            // 'remaining'  => $land->numRemaining(),
            // 'order_number'  => e($land->order_number),
            // 'purchase_cost'  => Helper::formatCurrencyOutput($land->purchase_cost),
            // 'purchase_date'  => Helper::getFormattedDateObject($land->purchase_date, 'date'),
            // 'qty'           => (int) $land->qty,
            'created_at' => Helper::getFormattedDateObject($land->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($land->updated_at, 'datetime'),
        ];

        $permissions_array['user_can_checkout'] = false;

        // if ($land->numRemaining() > 0) {
        //     $permissions_array['user_can_checkout'] = true;
        // }

        $permissions_array['available_actions'] = [
            'checkout' => Gate::allows('checkout', Land::class),
            'checkin' => Gate::allows('checkin', Land::class),
            'update' => Gate::allows('update', Land::class),
            'delete' => Gate::allows('delete', Land::class),
        ];
        $array += $permissions_array;
        return $array;
    }


    public function transformCheckedoutLands (Collection $land_users, $total)
    {

        $array = array();
        foreach ($land_users as $user) {
            $array[] = (new UsersTransformer)->transformUser($user);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }



}
