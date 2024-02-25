<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh']]);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        if ($user->status !== 'active') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $refreshToken = $this->createRefreshToken();
        return $this->respondWithToken($token, $refreshToken);
    }

    public function profile()
    {
        try {
            return response()->json(auth()->user());
        } catch (Exception $e) {
            return response()->json(['error' => 'user not found'], 404);
        }
    }
    public function logout()
    {
        $token = JWTAuth::parseToken()->getToken();
        JWTAuth::invalidate($token);
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function register(Request $request)
    {
        $validationRules = [
            'email' => 'required|string|email|unique:users,email',
            'name' => 'required|string',
            'password' => 'required|string',
            'phone' => 'required|string',
        ];

        $customMessages = [
            'required' => ':attribute dont empty.',
            'unique' => ':attribute has already been taken.',
            'email' => 'The format of :attribute is invalid.',
        ];

        $validatedData = $request->validate($validationRules, $customMessages);

        $user = new User();
        $user->email = $validatedData['email'];
        $user->name = $validatedData['name'];
        $user->password = bcrypt($validatedData['password']);
        $user->phone = $validatedData['phone'];
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('avatars', $imageName);
            $user->image = 'avatars/' . $imageName;;
        }
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();

        return response()->json([
            'message' => 'success',
            'user' => $user,
        ]);
    }
    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');
        try {
            // Giải mã refresh token
            $decoded = JWTAuth::getJWTProvider()->decode($refreshToken);

            // Kiểm tra xem giải mã có thành công không
            if ($decoded && isset($decoded['user_id'])) {
                $user = User::find($decoded['user_id']);
                if (!$user) {
                    return response()->json(['error' => 'User not found'], 404);
                }

                $token = auth()->login($user);
                $refreshToken = $this->createRefreshToken();

                return $this->respondWithToken($token, $refreshToken);
            } else {
                return response()->json(['error' => 'Invalid refresh token'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    protected function respondWithToken($token, $refreshToken)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'role' => auth()->user()->role,
            'token_type' => 'bearer',
        ]);
    }
    public function updatePassword(Request $request)
    {
        // Xác thực xem người dùng đã đăng nhập hay chưa
        if (!Auth::check()) {
            throw new AuthenticationException('Unauthenticated');
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        $user = Auth::user();
        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');

        // Kiểm tra xem mật khẩu hiện tại có đúng không
        if (!Hash::check($currentPassword, $user->password)) {
            return response()->json(['message' => 'Mật khẩu hiện tại không đúng'], 400);
        }

        // Nếu mật khẩu mới giống mật khẩu cũ, trả về thông báo lỗi
        if (Hash::check($newPassword, $user->password)) {
            return response()->json(['message' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại'], 400);
        }

        // Cập nhật mật khẩu mới cho người dùng
        User::where('id', $user->id)->update(['password' => bcrypt($newPassword)]);

        return response()->json(['message' => 'Đổi mật khẩu thành công']);
    }
    private function createRefreshToken()
    {
        $data = [
            'user_id' => auth()->user()->id,
            'random' => rand() . time(),
            'exp' => time() + config('jwt.refresh_ttl'),
        ];
        $refreshToken = JWTAuth::getJWTProvider()->encode($data);
        return $refreshToken;
    }
}
