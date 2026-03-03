<?php

namespace App\Policies;

use App\Models\Pieza;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PiezaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrador') || $user->hasRole('Usuario');
    }

    /**
     * Determine whether the user can view the model.
     */

    public function view(User $user, Pieza $pieza): bool
    {
        return $user->hasRole('Administrador')
            ||
            $pieza->user_id === $user->id;
    }

    public function update(User $authUser, Pieza $pieza): bool
    {
        return $authUser->id === $pieza->user_id
            || $authUser->hasRole('Administrador');
    }

    /**
     * Determina si tiene permiso para crear una pieza(no recibe la pieza porque todavía no existe.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Administrador')
            || $user->hasRole('Usuario');
    }

    /**
     * Puede eliminar el propietario de la pieza o un administrador.
     */
    public function delete(User $user, Pieza $pieza): bool
    {
        return $user->hasRole('Administrador')
            ||
            $pieza->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pieza $pieza): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pieza $pieza): bool
    {
        return false;
    }
}
