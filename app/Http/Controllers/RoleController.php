<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseHelper;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // bisa pakai all atau get
        $user = Role::all();
        return ResponseHelper::success($user, 'Get role success');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            // Logika jika gagal
            if ($validator->fails()) {
                return ResponseHelper::error('Validation error', $validator->errors(), 422);
            }
            // buat query insert/ create
            $user = Role::create([
                'name' => $request->name,
            ]);

            // kembalikan respon
            return ResponseHelper::success('Registration role success', $user, 201);
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
        $role = Role::find($id);
        return ResponseHelper::success('Get role by id success', $role);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(),['name' => 'required|string']);

                if($validator->fails()){
                    return ResponseHelper::error('Validation error', $validator->errors(), 422);
                }

                $data = [
                    'name' => $request->name,
                ];

                if($request->filled('password')){
                    $data['password'] = $request->password;
                }

                $user = Role::find($id);
                $user->update($data);
                return ResponseHelper::success('Update role success', $user);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Internal Server Error', $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = Role::destroy($id);
            return ResponseHelper::success('Delete role success', $user);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Internal Server Error', $th->getMessage(), 500);
        }
    }
}
