<?php

namespace App\Policies;

class ProcurementPolicy extends SnipePermissionsPolicy
{
    public function columnName()
    {
        return 'procurements';
    }
}
