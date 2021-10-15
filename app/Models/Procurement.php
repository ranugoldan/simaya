<?php

namespace App\Models;

use App\Http\Traits\UniqueUndeletedTrait;
use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

class Procurement extends Model
{
    protected $presenter = 'App\Presenters\ProcurementPresenter';
    use Presentable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'procurements';
    
    protected $rules = array(
        'procurement_tag'       => 'required|max:255|unique_undeleted',
        'status'                => 'max:255|nullable',
        'model_id'              => 'exists:models,id',
        'asset_id'              => 'exists:assets,id',
        'supplier_id'           => 'exists:suppliers,id',
        // 'qty'                   => 'required',
        'purchase_cost'         => 'numeric',
        'location_id'           => 'exists:locations,id',
        'department_id'         => 'exists:departments,id',
        'user_id'               => 'exists:users,id',
    );

    protected $casts = [
        'model_id'      => 'integer',
        'asset_id'      => 'integer',
        'supplier_id'   => 'integer',
        'qty'           => 'integer',
        'location_id'   => 'integer',
        'department_id' => 'integer',
        'user_id'       => 'integer',
    ];

    use ValidatingTrait;
    use UniqueUndeletedTrait;

    protected $fillable = [
        'procurement_tag',
        'status',
        'model_id',
        'asset_id',
        'supplier_id',
        'qty',
        'purchase_cost',
        'location_id',
        'department_id',
        'user_id',
    ];

    use Searchable;

    protected $searchableAttributes = [
        'procurement_tag',
        'status',
        'qty',
        'purchase_cost',
    ];

    protected $searchableRelations = [
        'models'        => ['name'],
        'assets'        => ['name'],
        'supplier'      => ['name'],
        'locations'     => ['name'],
        'department'    => ['name'],
        'user'          => ['name'],
    ];

    public function isDeletable()
    {
        return Gate::allows('delete', $this);
    }

    public function assets()
    {
        return $this->belongsToMany('\App\Models\Asset', 'procurement_assets', 'procurement_id', 'asset_id');
    }

    public function locations()
    {
        return $this->belongsToMany('\App\Models\Location', 'procurement_locations', 'procurement_id', 'location_id');
    }

    public function models()
    {
        return $this->belongsToMany('App\Models\AssetModel', 'procurement_models', 'procurement_id', 'model_id');
    }

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'department_id');
    }

    public function supplier()
    {
        return $this->hasOne('App\Models\Supplier', 'supplier_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'user_id');
    }

    public function scopeOrderAssets($query, $order)
    {
        return $query
            ->leftJoin('procurement_assets', 'procurements.id', '=', 'procurement_assets.procurement_id')
            ->leftJoin('assets', 'procurement_assets.asset_id', '=', 'assets.id')
            ->orderBy('assets.name', $order);
    }

    public function scopeOrderLocations($query, $order)
    {
        return $query
            ->leftJoin('procurement_locations', 'procurements.id', '=', 'procurement_locations.procurement_id')
            ->leftJoin('locations', 'procurement_locations.location_id', '=', 'locations.id')
            ->orderBy('locations.name', $order);
    }

    public function scopeOrderModels($query, $order)
    {
        return $query
            ->leftJoin('procurement_models', 'procurements.id', '=', 'procurement_models.procurement_id')
            ->leftJoin('models', 'procurement_models.model_id', '=', 'models.id')
            ->orderBy('models.name', $order);
    }

    public function scopeOrderDepartment($query, $order)
    {
        return $query
            ->leftJoin('departments', 'procurements.department_id', '=', 'departments.id')
            ->orderBy('departments.name', $order);
    }

    public function scopeOrderSupplier($query, $order)
    {
        return $query
            ->leftJoin('suppliers', 'procurements.supplier_id', '=', 'suppliers.id')
            ->orderBy('suppliers.name', $order);
    }

    public function scopeOrderUser($query, $order)
    {
        return $query
            ->leftJoin('users', 'procurements.user_id', '=', 'users.id')
            ->orderBy('users.name', $order);
    }

    public static function autoincrement_procurement()
    {
        $settings = \App\Models\Setting::getSettings();

        if ($settings->auto_increment_procurements == '1') {
            $temp_procurement_tag = \DB::table('procurements')
                ->max('procurement_tag');
            
            $procurement_tag_digits = preg_replace('/\D/', '', $temp_procurement_tag);
            $procurement_tag = preg_replace('/^0*/', '', $procurement_tag_digits);

            if ($settings->zerofill_count > 0) {
                return $settings->auto_increment_prefix.Procurement::zerofill($settings->next_auto_tag_base, $settings->zerofill_count);
            }

            return $settings->auto_increment_prefix.$settings->next_auto_tag_base;
        } else {
            return false;
        }
    }

    public static function zerofill($num, $zerofill = 3)
    {
        return str_pad($num, $zerofill, '0', STR_PAD_LEFT);
    }
}
