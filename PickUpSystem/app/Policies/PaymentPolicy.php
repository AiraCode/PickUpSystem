<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Admin bisa lihat semua, customer bisa lihat pembayaran miliknya
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCustomer();
    }

    /**
     * Admin lihat semua, customer hanya pembayaran untuk request miliknya
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer()) {
            return $payment->pickupRequest?->user_id === $user->id;
        }

        return false;
    }

    /**
     * Hanya admin yang bisa membuat pembayaran
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa mengupdate pembayaran
     */
    public function update(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa menghapus pembayaran
     */
    public function delete(User $user, Payment $payment): bool
    {
        return $user->isAdmin() && $payment->status === 'pending';
    }

    /**
     * Hanya admin yang bisa konfirmasi pembayaran
     */
    public function confirm(User $user, Payment $payment): bool
    {
        return $user->isAdmin() && $payment->status === 'pending';
    }
}
