<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PengaturanPajak;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengaturanPajakPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pengaturan::pajak');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PengaturanPajak $pengaturanPajak): bool
    {
        return $user->can('view_pengaturan::pajak');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_pengaturan::pajak');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PengaturanPajak $pengaturanPajak): bool
    {
        return $user->can('update_pengaturan::pajak');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PengaturanPajak $pengaturanPajak): bool
    {
        return $user->can('delete_pengaturan::pajak');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pengaturan::pajak');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, PengaturanPajak $pengaturanPajak): bool
    {
        return $user->can('force_delete_pengaturan::pajak');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_pengaturan::pajak');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, PengaturanPajak $pengaturanPajak): bool
    {
        return $user->can('restore_pengaturan::pajak');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_pengaturan::pajak');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, PengaturanPajak $pengaturanPajak): bool
    {
        return $user->can('replicate_pengaturan::pajak');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_pengaturan::pajak');
    }
}
