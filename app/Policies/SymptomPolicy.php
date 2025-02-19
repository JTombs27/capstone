<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Symptom;
use Illuminate\Auth\Access\HandlesAuthorization;

class SymptomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_symptom');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Symptom $symptom): bool
    {
        return $user->can('view_symptom');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_symptom');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Symptom $symptom): bool
    {
        return $user->can('update_symptom');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Symptom $symptom): bool
    {
        return $user->can('delete_symptom');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_symptom');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Symptom $symptom): bool
    {
        return $user->can('force_delete_symptom');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_symptom');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Symptom $symptom): bool
    {
        return $user->can('restore_symptom');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_symptom');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Symptom $symptom): bool
    {
        return $user->can('replicate_symptom');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_symptom');
    }
}
