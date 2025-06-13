<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    public function manage(User $user, Unit $unit)
    {
        return $user->hasRole('admin') || $unit->head_id == $user->id;
    }

    public function __construct() {}
}
