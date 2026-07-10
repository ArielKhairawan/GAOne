<?php

namespace App\Policies;

use App\Models\ConsumptionRequest;
use App\Models\User;

class ConsumptionRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('consumption.view');
    }

    public function view(User $user, ConsumptionRequest $request): bool
    {
        if (! $user->can('consumption.view')) {
            return false;
        }

        if ($user->hasRole('Karyawan') && ! $user->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            return $request->user_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('consumption.create');
    }

    public function approve(User $user, ConsumptionRequest $request): bool
    {
        return $user->can('consumption.approve');
    }
}
