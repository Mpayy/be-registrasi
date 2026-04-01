<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // untuk fungsi bawaan untuk validasi


class AuthController extends Controller
{
    public function registration(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed' // confirmed untuk verifikasi dua langkah
                // untuk mengetes buat payload
                // {
                //     "name" : "Achmad Rifaih",
                //     "email" : "rifaih712@gmail.com",
                //     "password" : "12345678",
                //     "password_confirmation" : "12345678"
                // }
            ]);

            // Logika jika gagal
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'error' => $validator->errors()
                ], 422); // Validasi nomer harus di set jika erros
            }
            // buat query insert/ create
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);

            // kembalikan respon
            return response()->json([
                'status' => true,
                'message' => 'Registration success',
                'data' => $user,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Internal server error', 
                'error' => $th->getMessage() // tidak boleh di munculkan saat produksi, hanya boleh saat development
                ],500);
        }  
    }

    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'error' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();
            if(!$user || !Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Credential',
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Login Success',
                'data' => $user,
                'token' => $token
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Intenal Server Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
