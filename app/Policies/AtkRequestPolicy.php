<?php

namespace App\Policies;

use App\Models\AtkRequest;
use App\Models\User;

class AtkRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('atk.view');
    }

    public function view(User $user, AtkRequest $request): bool
    {
        if (! $user->can('atk.view')) {
            return false;
        }

        if ($user->hasRole('Karyawan') && ! $user->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            return $request->user_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('atk.create');
    }

    public function approve(User $user, AtkRequest $request): bool
    {
        return $user->can('atk.approve');
    }
}
