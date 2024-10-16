<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tidak_masuk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class Tidak_masukPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tidak::masuk');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tidak_masuk $tidakMasuk): bool
    {
        if (auth()->user()->hasRole('karyawan')) {
            return $user->can('view_tidak::masuk') && $user->karyawan_id === $tidakMasuk->karyawan_id;
        }
        return $user->can('view_tidak::masuk');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tidak::masuk');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tidak_masuk $tidakMasuk): bool
    {
        if (auth()->user()->hasRole('karyawan')) {
            return $user->can('update_tidak::masuk') && $user->karyawan_id === $tidakMasuk->karyawan_id;
        }
        return $user->can('update_tidak::masuk');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tidak_masuk $tidakMasuk): bool
    {
        if (auth()->user()->hasRole('karyawan')) {
            return $user->can('delete_tidak::masuk') && $user->karyawan_id === $tidakMasuk->karyawan_id;
        }
        return $user->can('delete_tidak::masuk');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_tidak::masuk');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Tidak_masuk $tidakMasuk): bool
    {
        if (auth()->user()->hasRole('karyawan')) {
            return $user->can('force_delete_tidak::masuk') && $user->karyawan_id === $tidakMasuk->karyawan_id;
        }
        return $user->can('force_delete_tidak::masuk');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_tidak::masuk');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Tidak_masuk $tidakMasuk): bool
    {
        return $user->can('restore_tidak::masuk');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_tidak::masuk');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Tidak_masuk $tidakMasuk): bool
    {
        return $user->can('replicate_tidak::masuk');
    }

    public function approve(User $user, Tidak_masuk $tidakMasuk): bool
    {
        return $user->can('approve_tidak::masuk');
    }

    public function decline(User $user, Tidak_masuk $tidakMasuk): bool
    {
        return $user->can('decline_tidak::masuk');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_tidak::masuk');
    }
}
