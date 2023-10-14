<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Storage;// Import model User

class UserController extends Controller
{


/**
 * The index function retrieves all users from the database and returns their information, including
 * image URLs, in JSON format.
 * 
 * @return a JSON response containing an array of users with their respective image URLs.
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
                    'created_at' =>$user->created_at,
                    'updated_at' =>$user->updated_at

                ];
            });
            return response()->json(['data' => $usersWithImageUrls]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }


   /**
    * The function stores user data, including an optional image, and returns a JSON response with a
    * success message and the user data.
    * 
    * @param Request request The  parameter is an instance of the Request class, which
    * represents an HTTP request. It contains all the data and information about the incoming request,
    * such as the request method, headers, query parameters, form data, and uploaded files. In this
    * code snippet, the  parameter is used to
    * 
    * @return a JSON response with a message and data. The message is "Người dùng đã được tạo" (which
    * translates to "User has been created" in English) and the data is the newly created user object.
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
 * The function retrieves user data and returns it in JSON format, including the user's ID, name,
 * email, password, phone, avatar, role, and status.
 * 
 * @param string id The "id" parameter is a string that represents the unique identifier of a user. It
 * is used to retrieve the user's information from the database.
 * 
 * @return a JSON response containing the user data.
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
 * The function updates a user's information in a database, including their name, email, phone number,
 * status, avatar image, and password.
 * 
 * @param Request request The  parameter is an instance of the Request class, which represents
 * an HTTP request. It contains information about the request such as the request method, headers, and
 * input data.
 * @param string id The ID of the user that needs to be updated.
 * 
 * @return a JSON response with a message and data. The message indicates that the user information has
 * been updated, and the data contains the updated user object.
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

        return response()->json(['message' => 'Thông tin người dùng đã được cập nhật', 'data' => $user]);
    }


/**
 * The destroy function deletes a user with the given ID and returns a JSON response with a success
 * message.
 * 
 * @param string id The "id" parameter is a string that represents the unique identifier of the user
 * that needs to be deleted.
 * 
 * @return a JSON response. If the user is not found, it returns a JSON response with a message "Không
 * tìm thấy người dùng" and a status code of 404. If the user is found and successfully deleted, it
 * returns a JSON response with a message "Người dùng đã bị xóa".
 */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Người dùng đã bị xóa']);
    }

}
