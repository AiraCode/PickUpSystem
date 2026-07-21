<?php

namespace App\Policies;

use App\Models\PickupRequest;
use App\Models\User;

class PickupRequestPolicy
{
    /**
     * Admin bisa melihat semua request, customer hanya miliknya sendiri
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCustomer();
    }

    /**
     * Admin bisa lihat semua, customer hanya miliknya, driver hanya yang di-assign
     */
    public function view(User $user, PickupRequest $pickupRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer()) {
            return $user->id === $pickupRequest->user_id;
        }

        // Driver bisa lihat request yang ada di order-nya
        if ($user->isDriver()) {
            return $pickupRequest->orderDetail?->pickupOrder?->driver_id === $user->id;
        }

        return false;
    }

    /**
     * Hanya customer yang bisa membuat pickup request
     */
    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    /**
     * Customer bisa update request miliknya selama masih pending
     */
    public function update(User $user, PickupRequest $pickupRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer()) {
            return $user->id === $pickupRequest->user_id
                && $pickupRequest->status === 'pending';
        }

        return false;
    }

    /**
     * Customer bisa cancel request miliknya selama belum dijemput
     */
    public function delete(User $user, PickupRequest $pickupRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer()) {
            return $user->id === $pickupRequest->user_id
                && in_array($pickupRequest->status, ['pending', 'approved']);
        }

        return false;
    }

    /**
     * Hanya admin yang bisa approve/schedule request
     */
    public function approve(User $user, PickupRequest $pickupRequest): bool
    {
        return $user->isAdmin() && $pickupRequest->status === 'pending';
    }

    /**
     * Admin bisa assign jadwal, driver bisa update status picked_up
     */
    public function updateStatus(User $user, PickupRequest $pickupRequest): bool
    {
        return $user->isAdmin() || $user->isDriver();
    }
}
