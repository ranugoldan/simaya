<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Statuslabel;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Console\Command;

class Purge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipeit:purge {--force=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge all soft-deleted deleted records in the database. This will rewrite history for items that have been edited, or checked in or out. It will also rewrite history for users associated with deleted items.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $force = $this->option('force');
        if (($this->confirm("\n****************************************************\nTHIS WILL PURGE ALL SOFT-DELETED ITEMS IN YOUR SYSTEM. \nThere is NO undo. This WILL permanently destroy \nALL of your deleted data. \n****************************************************\n\nDo you wish to continue? No backsies! [y|N]")) ||  $force == 'true') {

            /**
             * Delete assets
             */
            $assets = Asset::whereNotNull('deleted_at')->withTrashed()->get();
            $assetcount = $assets->count();
            $this->info($assets->count().' assets purged.');
            $asset_assoc = 0;
            $asset_maintenances = 0;

            foreach ($assets as $asset) {
                $this->info('- Asset "'.$asset->present()->name().'" deleted.');
                $asset_assoc += $asset->assetlog()->count();
                $asset->assetlog()->forceDelete();
                $asset_maintenances += $asset->assetmaintenances()->count();
                $asset->assetmaintenances()->forceDelete();
                $asset->forceDelete();
            }

            $this->info($asset_assoc.' corresponding log records purged.');
            $this->info($asset_maintenances.' corresponding maintenance records purged.');

            $models = AssetModel::whereNotNull('deleted_at')->withTrashed()->get();
            $this->info($models->count().' asset models purged.');
            foreach ($models as $model) {
                $this->info('- Asset Model "'.$model->name.'" deleted.');
                $model->forceDelete();
            }


            $categories = Category::whereNotNull('deleted_at')->withTrashed()->get();
            $this->info($categories->count().' categories purged.');
            foreach ($categories as $category) {
                $this->info('- Category "'.$category->name.'" deleted.');
                $category->forceDelete();
            }

            $suppliers = Supplier::whereNotNull('deleted_at')->withTrashed()->get();
            $this->info($suppliers->count().' suppliers purged.');
            foreach ($suppliers as $supplier) {
                $this->info('- Supplier "'.$supplier->name.'" deleted.');
                $supplier->forceDelete();
            }

            $users = User::whereNotNull('deleted_at')->where('show_in_list', '!=', '0')->withTrashed()->get();
            $this->info($users->count().' users purged.');
            $user_assoc = 0;
            foreach ($users as $user) {
                $this->info('- User "'.$user->username.'" deleted.');
                $user_assoc += $user->userlog()->count();
                $user->userlog()->forceDelete();
                $user->forceDelete();
            }
            $this->info($user_assoc.' corresponding user log records purged.');

            $manufacturers = Manufacturer::whereNotNull('deleted_at')->withTrashed()->get();
            $this->info($manufacturers->count().' manufacturers purged.');
            foreach ($manufacturers as $manufacturer) {
                $this->info('- Manufacturer "'.$manufacturer->name.'" deleted.');
                $manufacturer->forceDelete();
            }

            $status_labels = Statuslabel::whereNotNull('deleted_at')->withTrashed()->get();
            $this->info($status_labels->count().' status labels purged.');
            foreach ($status_labels as $status_label) {
                $this->info('- Status Label "'.$status_label->name.'" deleted.');
                $status_label->forceDelete();
            }


        } else {
            $this->info('Action canceled. Nothing was purged.');
        }

    }
}
