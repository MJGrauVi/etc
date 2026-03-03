<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    //La policy define que puede hacer cada usuario autenticado sobre un modelo concreto, en este caso User.
    //Indica que puede hacer un usuario sobre otros.
    /**
     * Ver listado de usuarios(usuario con rol especificado).
     */
    public function viewAny(User $authUser): bool
    {
        //Un usuario autenticado puede verlos.
        //return true;

        //Solo los administradores podrán ver todos los usuarios.
        return $authUser->hasRole('Administrador');
    }

    /**
     * Un usuario autenticado puede ver su propio perfil y el administrador cualquiera.
     */
    public function view(User $authUser, User $user): bool
    {

        return $authUser->hasRole('Administrador') || $authUser->id === $user->id;

        // Con roles:
        // return $authUser->hasRole('Admin') || $authUser->id === $user->id;
    }

    /**
     * Solo puedes crear usuarios con rol de Administrador.
     */
    /*public function create(User $User): bool
    {
        return true;
    }*/
    public function create(User $authUser): bool
    {
        return $authUser->hasRole('Administrador');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $user): bool
    {
        return $authUser->hasRole('Administrador') || $authUser->id === $user->id;
    }

    /**
     * Solo puede eliminar un usuario con rol de administraor.
     */
    public function delete(User $authUser, User $user):bool
    {
        return $authUser->id === $user->id || $authUser->hasRole('Administrador');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $authUser, User $model): bool
    {
        return $authUser->hasRole('Administrador');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $authUser, User $model): bool
    {
        return $authUser->hasRole('Administrador');
    }
}
