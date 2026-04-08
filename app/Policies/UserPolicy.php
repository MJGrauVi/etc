<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    //La policy define que puede hacer cada usuario autenticado sobre un modelo concreto, en este caso User.
    //Indica que puede hacer un usuario sobre otros.
    /**
     * Ver listado de usuarios.
     * La clase Response permite devolver más información cuando deniegas un permiso.
     */
    public function viewAny(User $authUser)
    {
        //Un usuario autenticado puede verlos.
        //return true;

        //Solo los administradores podrán ver todos los usuarios.
        return $authUser->hasRole('Administrador') ? Response::allow() : Response::deny('Solo los administradores pueden ver el listado de usuarios.');
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
    public function create(?User $authUser): bool
    {
        //return $authUser->hasRole('Administrador');
        return true;
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
    /*
     * Función específica para cambiar rol a un usuario.
     * */
    public function changeRole(User $authUser, User $user): bool{
        //Un Administrador no puede cambiar su propio rol.
        if($authUser->id === $user->id){
            return false;
        }
        return $authUser->hasRole('Administrador');
    }
}
