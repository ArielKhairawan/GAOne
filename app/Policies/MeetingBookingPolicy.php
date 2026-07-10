<?php

namespace App\Policies;

use App\Models\MeetingBooking;
use App\Models\User;

class MeetingBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('meeting.view');
    }

    public function view(User $user, MeetingBooking $booking): bool
    {
        if (! $user->can('meeting.view')) {
            return false;
        }

        if ($user->hasRole('Karyawan') && ! $user->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            return $booking->user_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('meeting.create');
    }

    public function update(User $user, MeetingBooking $booking): bool
    {
        return $user->can('meeting.edit');
    }

    public function approve(User $user, MeetingBooking $booking): bool
    {
        return $user->can('meeting.approve');
    }
}
