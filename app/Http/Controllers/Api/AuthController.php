<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['expect'=>['login','register']]);
    }

    public function login(Request $request){
        $request->validate([
            'email'=> 'required|string|email',
            'password'=> 'required|string',
        ]);
        $credentials = $request->only('email','password');

        $token = Auth::attempt($credentials);
        if(!$token){
            return response()->json([
                'status'=>'error',
                'message'=>'Unauthorized',
            ],401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'auth' => [
                'token'=> $token,
                'type'=> 'bearer',
                ]
            ]);
    }

    public function register(Request $request){
        $request->validate([
            'name'=> 'required|string|mas:255',
            'email'=> 'required|string|email|max:255|unique:users',
            'password'=> 'required|string|min:6',
        ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User Created Success',
            'data' => $user,
            'auth' => [
                'token'=> $token,
                'type'=> 'bearer',
                ]
            ]);
    }
}