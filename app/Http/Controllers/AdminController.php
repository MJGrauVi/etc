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

        return User::select('id', 'name', 'email', 'email_verified_at', 'created_at')
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function cambiarRol(Request $request, User $user)
    {
        // Comprueba changeRole de UserPolicy automáticamente
        $this->authorize('changeRole', $user);

        $user->roles()->update(['nombre' => $request->rol]);
        return response()->json(['message' => 'Rol actualizado']);
    }
}
