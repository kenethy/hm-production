<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ResourcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all resources
        if ($user->isAdmin()) {
            return true;
        }

        // Staff can only view specific resources
        if ($user->isStaff()) {
            // Get the current resource class name
            $resourceClass = request()->route('resource');
            
            // Allow access only to BookingResource and ServiceResource
            $allowedResources = [
                'bookings',
                'services',
            ];
            
            return in_array($resourceClass, $allowedResources);
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }
}
