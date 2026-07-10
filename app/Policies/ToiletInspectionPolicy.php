<?php

namespace App\Policies;

use App\Models\ToiletInspection;
use App\Models\User;

class ToiletInspectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('toilet.view');
    }

    public function view(User $user, ToiletInspection $inspection): bool
    {
        return $user->can('toilet.view');
    }

    public function create(User $user): bool
    {
        return $user->can('toilet.create');
    }

    public function update(User $user, ToiletInspection $inspection): bool
    {
        if (! $user->can('toilet.edit')) {
            return false;
        }

        if ($user->hasRole('Petugas Kebersihan') && ! $user->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            return $inspection->petugas_id === $user->id;
        }

        return true;
    }

    public function delete(User $user, ToiletInspection $inspection): bool
    {
        return $user->can('toilet.delete');
    }
}
