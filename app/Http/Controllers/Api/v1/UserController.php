<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Import model User

class UserController extends Controller
{

    /**
     * @OA\Info(
     *   title="User API Documentation",
     *   version="1.0.0"
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/v1/user",
     *     summary="Lấy danh sách người dùng",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     @OA\Response(response=200, description="Danh sách người dùng"),
     *     @OA\Response(response=500, description="Lỗi server"),
     * )
     */
    public function index()
    {
        try {
            $users = User::all();

            $usersWithImageUrls = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->image,
                    'role' => $user->role,
                    'status' => $user->status,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at

                ];
            });
            return response()->json(['data' => $usersWithImageUrls]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/user",
     *     summary="Tạo người dùng mới",
     *     operationId="createUser",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="image", type="string", format="binary"),
     *             @OA\Property(property="status", type="string", default="active"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Người dùng đã được tạo"),
     * )
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->phone = $request->input('phone');
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('avatars', $imageName);
            $user->image = 'avatars/' . $imageName;
        }

        $user->role = 'root';
        $user->status = $request->input('status', 'active');
        $user->save();

        return response()->json(['message' => 'Người dùng đã được tạo', 'data' => $user]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user/{id}",
     *     summary="Lấy thông tin người dùng theo ID",
     *     operationId="getUserById",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID của người dùng", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Thông tin người dùng"),
     *     @OA\Response(response=404, description="Không tìm thấy người dùng"),
     * )
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password, // Bao gồm mật khẩu
            'phone' => $user->phone,
            'avatar' => asset("storage/{$user->avatar}"),
            'role' => $user->role,
            'status' => $user->status,
        ];

        return response()->json(['data' => $userData]);
    }


    /**
     * @OA\Put(
     *     path="/api/v1/user/{id}",
     *     summary="Cập nhật thông tin người dùng",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID của người dùng", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="image", type="string", format="binary"),
     *             @OA\Property(property="status", type="string", default="active"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Thông tin người dùng đã được cập nhật"),
     *     @OA\Response(response=404, description="Không tìm thấy người dùng"),
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->status = $request->input('status', 'active');

        if ($request->hasFile('image')) {
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('avatars', $imageName);
            $user->avatar = 'avatars/' . $imageName;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return response()->json(['message' => 'success', 'data' => $user]);
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/user/{id}",
     *     summary="Xóa người dùng theo ID",
     *     operationId="deleteUser",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID của người dùng", @OA\Schema(type="string")),
     *     @OA\Response(response=204, description="Người dùng đã bị xóa"),
     *     @OA\Response(response=404, description="Không tìm thấy người dùng"),
     * )
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }
        $user->delete();

        return response()->json(['message' => 'success']);
    }
}
