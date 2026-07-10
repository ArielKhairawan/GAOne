<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('vehicle.view');
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        if (! $user->can('vehicle.view')) {
            return false;
        }

        if ($user->hasRole('Driver') && ! $user->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            return $vehicle->driver_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('vehicle.create');
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->can('vehicle.edit');
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->can('vehicle.delete');
    }
}
