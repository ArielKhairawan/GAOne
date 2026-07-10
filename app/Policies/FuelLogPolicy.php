<?php

namespace App\Policies;

use App\Models\FuelLog;
use App\Models\User;

class FuelLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('fuel.view');
    }

    public function view(User $user, FuelLog $fuelLog): bool
    {
        if (! $user->can('fuel.view')) {
            return false;
        }

        if ($user->hasRole('Driver') && ! $user->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            return $fuelLog->driver_id === $user->id || $fuelLog->vehicle?->driver_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('fuel.create');
    }

    public function update(User $user, FuelLog $fuelLog): bool
    {
        if (! $user->can('fuel.edit')) {
            return false;
        }

        if ($user->hasRole('Driver') && ! $user->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            return $fuelLog->driver_id === $user->id || $fuelLog->vehicle?->driver_id === $user->id;
        }

        return true;
    }

    public function delete(User $user, FuelLog $fuelLog): bool
    {
        return $user->can('fuel.delete');
    }
}
