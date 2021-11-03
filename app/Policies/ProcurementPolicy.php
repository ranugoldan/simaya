<?php

namespace App\Policies;

use App\Models\User;

class ProcurementPolicy extends SnipePermissionsPolicy
{
    public function columnName()
    {
        return 'procurements';
    }

    public function approve(User $user)
    {
        return $user->hasAccess('procurements.approve');
    }

    public function assign(User $user)
    {
        return $user->hasAccess('procurements.assign');
    }
}
