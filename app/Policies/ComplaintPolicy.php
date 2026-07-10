<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('complaint.view');
    }

    public function view(User $user, Complaint $complaint): bool
    {
        if (! $user->can('complaint.view')) {
            return false;
        }

        if ($user->hasRole('Karyawan') && ! $user->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            return $complaint->user_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('complaint.create');
    }

    public function update(User $user, Complaint $complaint): bool
    {
        return $user->can('complaint.edit');
    }
}
