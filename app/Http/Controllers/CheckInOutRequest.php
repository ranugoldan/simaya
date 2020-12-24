<?php
namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\SnipeModel;
use App\Models\User;

trait CheckInOutRequest
{
    /**
     * Find target for checkout
     * @return SnipeModel        Target asset is being checked out to.
     */
    protected function determineCheckoutTarget()
    {
          // This item is checked out to a location
        switch(request('checkout_to_type'))
        {
            case 'user':
                return User::findOrFail(request('assigned_user'));
        }
        return null;
    }
}
