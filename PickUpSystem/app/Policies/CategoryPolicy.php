<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Semua user bisa melihat daftar kategori
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Semua user bisa melihat detail kategori
     */
    public function view(?User $user, Category $category): bool
    {
        return true;
    }

    /**
     * Hanya admin yang bisa membuat kategori baru
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa mengupdate kategori
     */
    public function update(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa menghapus kategori
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }
}
