<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        // Comprueba viewAny de UserPolicy automáticamente
        $this->authorize('viewAny', User::class);

        $users = User::select('id', 'nombre', 'email', 'email_verified_at', 'created_at')
            ->with('roles:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'error' => false,
            'data'  => $users
        ]);
    }

    public function cambiarRol(Request $request, User $user)
    {
        // Comprueba changeRole de UserPolicy automáticamente
        $this->authorize('changeRole', $user);

        // Spatie tiene syncRoles para cambiar el rol de un usuario.
        $user->syncRoles([$request->rol]);

        return response()->json([
            'error'   => false,
            'message' => 'Rol actualizado correctamente',
            'data'    => $user->load('roles:id,name')
        ]);
    }
}
