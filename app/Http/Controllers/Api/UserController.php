<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'            
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "status" => 1,
            "access_token" => $token,
            "user" => $user,
            "msg" => "¡Registro de usuario exitoso!",
        ]);    
    }


    public function updateUsers(Request $request)
    {

        $user = auth()->user();

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            "user" => $user,
            "msg" => "¡Usuario actualizado exitosamente!",
        ]);
    }


    public function login(Request $request) {

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where("email", "=", $request->email)->first();

        if( isset($user->id) ){
            if (Hash::check($request->password, $user->password)) {
                //creo el token
                $token = $user->createToken("auth_token")->plainTextToken;
                // Define la expiración del token
                $expiration = now()->addHours(4);
                // Actualiza la fecha de expiración del token
                $user->tokens()->where('name', 'auth_token')->update(['expires_at' => $expiration]);
            
                return response()->json([
                    "status" => 1,
                    "msg" => "¡Usuario logueado exitosamente!",
                    "access_token" => $token,
                    "user" => $user
                ]);        
            }else{
                return response()->json([
                    "status" => 0,
                    "msg" => "La password es incorrecta",
                ], 404);    
            }

        }else{
            return response()->json([
                "status" => 0,
                "msg" => "Usuario no registrado",
            ], 404);  
        }
    }

    public function userProfile() {
        $user = auth()->user();

        return response()->json([
            "status" => 0,
            "msg" => "Acerca del perfil de usuario",
            "data" => [
                "id" => $user-> id,
                "name" => $user-> name,
                "email" => $user-> email,
            ]
        ]); 
    }

    public function countUsers() {
    $count = User::count();
    return $count;
    }

    public function logout() {
        auth()->user()->tokens()->delete();
        
        return response()->json([
            "status" => 1,
            "msg" => "Cierre de Sesión",            
        ]); 
    }
}
