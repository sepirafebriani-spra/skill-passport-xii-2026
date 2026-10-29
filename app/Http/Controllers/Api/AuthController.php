<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "email" => "required|email|string",
            "password" => "required|string",
        ]);

        if($validator->fails()){
            return response()->json([
                "message" => "Validator gagal",
                "error" => $validator->errors()
            ], 422);
        }

        $user = User::where("email", $request->email)->first();

        if(!$user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "Email atau password salah",
            ], 401);
        }

        $token = $user->createToken("apiToken")->plainTextToken;

        return response()->json([
            "message" => "Login Berhasil",
            "user" => $user,
            "token" => $token,
            "token_type" => "bearer"
        ], 200);
    }

    public function logout(Request $request)
    {
        // Perbaiki typo dari currentAccesToken menjadi currentAccessToken
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "Anda Berhasil logout",
        ], 200);
    }
}
