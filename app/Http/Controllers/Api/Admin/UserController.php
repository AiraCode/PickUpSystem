<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with('warehouse')->get();

        return response()->json([
            'message' => 'Daftar admin berhasil diambil',
            'data' => $users,
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['warehouse_id'] = isset($data['warehouse_id']) && $data['warehouse_id'] !== '' ? $data['warehouse_id'] : null;
        $data['role'] = $data['warehouse_id'] ? 'warehouse' : 'central';

        $user = User::create($data);

        return response()->json([
            'message' => 'Admin baru berhasil ditambahkan',
            'data' => $user,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'message' => 'Detail admin berhasil diambil',
            'data' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (array_key_exists('warehouse_id', $data)) {
            $data['warehouse_id'] = $data['warehouse_id'] !== '' ? $data['warehouse_id'] : null;
            $data['role'] = $data['warehouse_id'] ? 'warehouse' : 'central';
        }

        $user->update($data);

        return response()->json([
            'message' => 'Data admin berhasil diperbarui',
            'data' => $user,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Admin berhasil dihapus',
        ]);
    }
}
