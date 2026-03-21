<?php

namespace App\Policies;

use App\Models\Pieza;
use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PublicacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user) {
        // Admin puede ver todas
        if ($user->hasRole('Administrador')) {
            return true;
        }
        // Usuarios normales pueden ver sus propias publicaciones.
        return true; // Permitimos el acceso al listado, pero filtraremos en el controlador.
        }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Publicacion $publicacion): bool
    {
        return $user->hasRole('Administrador') || $publicacion->pieza->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Pieza $pieza)
    {
        return $user->hasRole('Administrador')
            || $pieza->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Publicacion $publicacion)
    {
        return $user->hasRole('Administrador')
            || $publicacion->pieza->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Publicacion $publicacion)
    {
        return $user->hasRole('Administrador')
            || $publicacion->pieza->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Publicacion $publicacion): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Publicacion $publicacion): bool
    {
        return false;
    }
    /**
     * Determina si el usuario puede generar contenido con IA para una pieza.
     */
    public function generate(User $user, Pieza $pieza): bool
    {
        // El admin puede todo, o el dueño de la pieza puede generar contenido para ella
        return $user->hasRole('Administrador') || $pieza->user_id === $user->id;
    }
}
