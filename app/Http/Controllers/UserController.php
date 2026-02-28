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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ReadAllUsersRequest $request)
    {
        if (Auth::user()->hasRole('Admin')){
            return User::all();
        }else{
            return response([
                "error"=>true,
                "message"=>"no se tiene permisos para ver estos datos"
            ],403);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //dd($request);
        return User::create($request->all());

    }

    /**
     * Display the specified resource.
     */
    public function show(ShowUserRequest $request, User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        /*$user->email=$request->email??$user->email;
        $user->password=$request->password??$user->password;
        $user->name=$request->name??$user->name;*/

        $user->update([
            "email"=>$request->email??$user->email,
            "password" => $request->password??$user->password,
            "name"=>$request->name??$user->name
        ]);
        //$user->save();




    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function verify(LoginUserRequest $request){
        Auth::attempt([
            "email"=>$request->email,
            "password"=>$request->password
        ]);

        $usuario = Auth::user();

        if(!$usuario){
            return response([
                "error"=>true,
                "message"=>'No se ha podido autenticar el usuario',
                "code"=>403
            ],403);
        }else{
            $token = $usuario->createToken('auth_token')->plainTextToken;
            return response([
                "error"=>false,
                "message"=>'Usuario autenticado correctamente',
                "token"=>$token,
                "type_token"=>"Bearer",
                "code"=>200
            ],200);
        }
    }

    public function logout(){

        if (!Auth::user()->tokens()->delete()) {
            return response([
                "error"=>true,
                "message"=>'No se ha podido hacer logout del usuario',
                "code"=>403
            ],403);
        }else{
            return response([
                "error"=>false,
                "message"=>'Cierre de session correcto',
                "code"=>200
            ],200);
        }
    }
}
