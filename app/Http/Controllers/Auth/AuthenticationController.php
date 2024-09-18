<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    //

    public function register(RegisterRequest  $registerRequest)  {
        //
        try {
            //
            $registerRequest->validated();

            $user = User::create([
                
                'name' => $registerRequest->name,
                'username' => $registerRequest->username,
                'email' => $registerRequest->email,
                'password' => Hash::make($registerRequest->password),
            ]);

            $token = $user->createToken('threads')->plainTextToken;

            
            //throw $th;
            return response([
                'user' =>  $user,
                'token' => $token
            ], 200);

        } catch (\Exception $e) {
            //throw $th;
            return response([
                'message' =>  $e->getMessage(),
            ], 500);
        }
        
    }

    public function login(LoginRequest $loginRequest){
        try {
            $loginRequest->validated();

            //check user

            $user = User::whereUsername($loginRequest->username)->first();
            
            if(!$user || !Hash::check($loginRequest->password, $user->password)){
                return response([
                    'message' => 'Invalide Credentials',
                ], 422);
            }

            $token = $user->createToken('threads')->plainTextToken;

            
            //throw $th;
            return response([
                'user' =>  $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            return response([
                'message' =>  $e->getMessage(),
            ], 500);
        }
    }
}
