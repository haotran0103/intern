<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

/**
 * The login function validates the email and password provided in the request, attempts to
 * authenticate the user, and returns a JSON response with the user information and an authorization
 * token if successful.
 * 
 * @param Request request The `` parameter is an instance of the `Illuminate\Http\Request`
 * class. It represents the HTTP request made to the server and contains information such as the
 * request method, headers, and input data.
 * 
 * @return a JSON response. If the login is successful, it returns a success status, the user object,
 * the password used for the login, and an authorization token. If the login fails, it returns an error
 * status and a message indicating that the login is unauthorized.
 */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'pass' => $request->password,
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'pass' => $request->password,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

/**
 * The register function creates a new user with the provided information, including an optional image,
 * and returns a JSON response with the user details and an authorization token.
 * 
 * @param Request request The  parameter is an instance of the Request class, which represents
 * an HTTP request. It contains all the data and information about the request, such as the request
 * method, headers, query parameters, form data, and uploaded files. In this code snippet, the 
 * parameter is used to retrieve
 * 
 * @return The code is returning a JSON response with the following data:
 */
    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->phone = $request->phone;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('avatars', $imageName);
            $user->image='avatars/' . $imageName;;
        }
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'pass' => $request->password,
            'authorization' => [                
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

 /**
  * The above function logs out the user and returns a JSON response indicating the success of the
  * logout operation.
  * 
  * @return a JSON response with a status of 'success' and a message of 'Successfully logged out'.
  */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

/**
 * The function refresh() returns a JSON response with the status, user information, and a refreshed
 * authorization token.
 * 
 * @return a JSON response with the following data:
 */
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
