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
    public function index(Request $request)
    {
        // Llama a UserPolicy@viewAny
        $this->authorize('viewAny', User::class);
        return response([
            "error" => false,
            "data" => User::all()
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
   /* public function store(StoreUserRequest $request)
    {
        //dd($request);
        $user = User::create($request->all('nombre','direccion','telefono','email', 'password'));
        if(!$user){
            return response([
                "error"=>true,
                "messaje"=>"No se ha podido crear el usuario en la bbdd."
            ],500);
        }else{
            return response([
                "error"=>false,
                "messaje"=>"Usuario creado correctamente.",
                "data"=>$user
            ],201);
        }
       // dump("Estoy en el controlador para guardar un usuario.");

    }*/
    public function store(StoreUserRequest $request) {
        $this->authorize('create', User::class);
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
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
            "data" => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Actualizar usuario
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        $this->authorize('update', $user);
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return response()->json($user);
      /*  return response([
            "error" => false,
            "message" => "Usuario actualizado correctamente.",
            "data" => $user
        ], 200);*/
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
                "token_type"=>"Bearer"
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

}
