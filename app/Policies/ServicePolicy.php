<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true; // Semua user dapat melihat daftar servis
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Service  $service
     * @return bool
     */
    public function view(User $user, Service $service): bool
    {
        return true; // Semua user dapat melihat detail servis
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true; // Semua user dapat membuat servis baru
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Service  $service
     * @return bool
     */
    public function update(User $user, Service $service): bool
    {
        return true; // Semua user dapat mengupdate servis
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Service  $service
     * @return bool
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->isAdmin(); // Hanya admin yang dapat menghapus servis
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Service  $service
     * @return bool
     */
    public function restore(User $user, Service $service): bool
    {
        return $user->isAdmin(); // Hanya admin yang dapat memulihkan servis
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Service  $service
     * @return bool
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return $user->isAdmin(); // Hanya admin yang dapat menghapus permanen servis
    }

    /**
     * Determine whether the user can edit mechanics.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function editMechanics(User $user): bool
    {
        return $user->isAdmin(); // Hanya admin yang dapat mengedit montir
    }

    /**
     * Determine whether the user can mark service as completed.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function markAsCompleted(User $user): bool
    {
        return $user->isAdmin(); // Hanya admin yang dapat menandai servis sebagai selesai
    }
}
