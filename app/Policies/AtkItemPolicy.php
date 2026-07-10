<?php

namespace App\Policies;

use App\Models\AtkItem;
use App\Models\User;

class AtkItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('atk.view');
    }

    public function view(User $user, AtkItem $item): bool
    {
        return $user->can('atk.view');
    }

    public function create(User $user): bool
    {
        return $user->can('atk.create');
    }

    public function update(User $user, AtkItem $item): bool
    {
        return $user->can('atk.edit');
    }

    public function delete(User $user, AtkItem $item): bool
    {
        return $user->can('atk.delete');
    }
}
