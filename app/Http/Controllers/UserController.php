<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\ReadAllUsersRequest;
use App\Http\Requests\ShowUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles:id,name')->get();

     /*   $users->each(function ($user) {
            $user->rol = $user->roles->pluck('name')->first();
            unset($user->roles);
        });*/
        return response()->json([
            "error" => false,
            "data" => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreUserRequest $request) {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        //Crear usuario.
        $user = User::create($data);

        //>Crear perfil vacio asociado.
        $user->perfil()->create([
            'tipo_documento' => null,
            'documento' => null,
            'movil' => null,
            'logo' => null,
            'descripcion' => null,
            'web' => null,
            'redes_sociales' => null,
        ]);
        return response([
            "error" => false,
            "message" => "Usuario creado correctamente.",
            "data" => $user
        ], 201);
    }
    /**
     * Mostrar un usuario.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return response([
            "error" => false,
            "data" => $user->load('roles')
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
  /*
    public function update(UpdateUserRequest $request, User $user)
    {

        $this->authorize('update', $user);
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return response()->json($user);
  }
      /*  return response([
            "error" => false,
            "message" => "Usuario actualizado correctamente.",
            "data" => $user
        ], 200);*/
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $rol = $data['rol'] ?? null;
        unset($data['rol']);
        $user->update($data);

        if($rol){
            $this->authorize('changeRole', $user);
            $user->syncRoles($rol);//Actualiza en rol en model_has_roles.
        }
        return response([
            "error" => false,
            "message" => "Usuario actualizado correctamente.",
            "data" => $user->load('roles')//Load hace que veamos el rol en postman.
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //Comprobación delegada a la policy.
  /*      if ($request->user()->id !== $user->id) {
            return response([
                "error" => true,
                "message" => "No autorizado."
            ], 403);
        }*/
       // dd($user);
       $this->authorize('delete', $user);
        $user->delete();
        return response([
            "error" => false,
            "message" => "Usuario eliminado correctamente.",
        ],200);
    }

    public function verify(LoginUserRequest $request){
        $autenticado = Auth::attempt([
            "email"=>$request->email,
            "password"=>$request->password
        ]);
        if(!$autenticado){
            return response([
                "error"=>true,
                "message"=>"Credenciales incorrectas.",
            ],401);

        }else{
            $user=Auth::user();
            $token=$user->createToken("auth-token")->plainTextToken;
            return response([
                "error"=>false,
                "message"=>"Usuario autenticado correctamente.",
                "token"=>$token,
                "token_type"=>"Bearer",
                "data" => $user
            ],200);
        }
    }

    public function logout(Request $request) {
        $token = $request->user()->currentAccessToken();
        if ($token) { $token->delete();
        } return response([
            "error" => false,
            "message" => "Cierre de sesión correcto.",
            "code" => 200
        ], 200);
    }

    //Obtener el perfil del usuario autenticado.
/*    public function perfil(Request $request){
        $user = $request->user()->load('roles');
        return response([
            "error" => false,
            "data" => $user
        ],200);
    }*/

}
