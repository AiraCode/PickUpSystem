<?php

namespace App\Policies;

use App\Models\PickupOrder;
use App\Models\User;

class PickupOrderPolicy
{
    /**
     * Admin bisa melihat semua order, driver hanya miliknya
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDriver();
    }

    /**
     * Admin bisa lihat semua, driver hanya yang di-assign ke dia
     */
    public function view(User $user, PickupOrder $pickupOrder): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDriver()) {
            return $user->id === $pickupOrder->driver_id;
        }

        return false;
    }

    /**
     * Hanya admin yang bisa membuat pickup order
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin bisa update semua, driver bisa update order miliknya
     */
    public function update(User $user, PickupOrder $pickupOrder): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDriver()) {
            return $user->id === $pickupOrder->driver_id
                && in_array($pickupOrder->status, ['pending', 'in_progress']);
        }

        return false;
    }

    /**
     * Hanya admin yang bisa menghapus/cancel order
     */
    public function delete(User $user, PickupOrder $pickupOrder): bool
    {
        return $user->isAdmin() && $pickupOrder->status === 'pending';
    }

    /**
     * Driver bisa mulai perjalanan jika order miliknya
     */
    public function start(User $user, PickupOrder $pickupOrder): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDriver()
            && $user->id === $pickupOrder->driver_id
            && $pickupOrder->status === 'pending';
    }

    /**
     * Driver bisa selesaikan perjalanan jika sedang in_progress
     */
    public function complete(User $user, PickupOrder $pickupOrder): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDriver()
            && $user->id === $pickupOrder->driver_id
            && $pickupOrder->status === 'in_progress';
    }
}
