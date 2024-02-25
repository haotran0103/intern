<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

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
            return response()->json(['message' => 'success', 'data' => $usersWithImageUrls]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

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
        
        if ($request->input('role') === 'root') {
            return response()->json(['message' => 'Không được phép thay đổi vai trò thành "root"'], 403);
        }
        if ($request->has('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return response()->json(['message' => 'success', 'data' => $user]);
    }
    public function destroy(string $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'success'], 404);
        }
    }
}
