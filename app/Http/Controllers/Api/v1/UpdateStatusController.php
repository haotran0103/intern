<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\subcategory;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateStatusController extends Controller
{
    /**
     * @OA\Info(
     *   title="User Status API Documentation",
     *   version="1.0.0"
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/v1/update-status/user",
     *     summary="Cập nhật trạng thái của người dùng (active/inactive)",
     *     operationId="updateUserStatus",
     *     tags={"User Status"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Trạng thái người dùng đã được cập nhật"),
     *     @OA\Response(response=404, description="Không tìm thấy người dùng"),
     * )
     */
    public function userStatus(Request $request){
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }
        if ($user->status === 'active') {
            $user->status = 'inactive';
        } else {
            $user->status = 'active';
        }

        $user->save();

        return response()->json(['message' => 'success']);
    }
    public function postStatus(Request $request)
    {
        $Post = Post::find($request->id);

        if (!$Post) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }
        if ($Post->status === 'active') {
            $Post->status = 'inactive';
        } else {
            $Post->status = 'active';
        }

        $Post->save();

        return response()->json(['message' => 'success']);
    }
}
