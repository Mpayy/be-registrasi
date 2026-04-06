<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseHelper;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // bisa pakai all atau get
        $user = User::all();
        return response()->json([
            'status' => true,
            'message' => 'Get user success',
            'data' => $user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8' // confirmed untuk verifikasi dua langkah
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
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json([
            'status' => true,
            'message' => 'Get user by id success',
            'data' => $user
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email,'.$id,
                    // 'password' => 'min:8'
                ]);

                if($validator->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation error'
                    ], 422);
                }

                $data = [
                    'name' => $request->name,
                    'email' => $request->email
                ];
            $user = User::find($id);
                if($request->filled('password')){
                    $data['password'] = $request->password;
            } else {
                $data['password'] = $user->password;
                }


            $user->update($data);
                return response()->json([
                    'status' => true,
                    'message' => 'Update user success',
                    'data' => $user
                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::destroy($id);
            return response()->json([
                'status' => true,
                'message' => 'Delete User Success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
