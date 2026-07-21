<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    /**
     * Admin dan driver bisa melihat daftar kendaraan
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDriver();
    }

    /**
     * Admin bisa lihat semua, driver hanya kendaraan yang di-assign
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDriver()) {
            return $user->id === $vehicle->driver_id;
        }

        return false;
    }

    /**
     * Hanya admin yang bisa menambah kendaraan
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa mengupdate kendaraan
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa menghapus kendaraan
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }
}
