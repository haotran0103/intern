<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\bannerImage;
use App\Models\Post;
use App\Models\post_history;
use App\Models\User;
use App\Models\user_activity;
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
    public function userStatus(Request $request)
    {
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

    /**
     * @OA\Post(
     *     path="/api/v1/update-status/post",
     *     summary="Cập nhật trạng thái của bài viết (active/inactive)",
     *     operationId="updatePostStatus",
     *     tags={"User Status"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Trạng thái bài viết đã được cập nhật"),
     *     @OA\Response(response=404, description="Không tìm thấy bài viết"),
     * )
     */
    public function postStatus(Request $request,$idu)
    {
        $post = Post::find($request->id);

        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $oldPostData = $post->toArray();

        if ($post->status === 'active') {
            $post->status = 'inactive';
        } else {
            $post->status = 'active';
        }

        $post->save();
        $user_id = $idu ?? 1;
        $post_history = new post_history;
        $post_history->user_id = $user_id;
        $post_history->post_id = $post->id;
        $post_history-> action = 'Updated post status to ' . $post->status;
        $post_history ->  previous_data = json_encode($oldPostData);
        $post_history ->  previous_data = json_encode($post->toArray());
        $post_history ->  action_time = now();
        $post_history->save();

        $user_activity = new user_activity;
        $user_activity->user_id = $user_id;
        $user_activity-> activity_type = 'Updated post status to ' . $post->status;
        $user_activity ->  activity_time = now();
        $user_activity->save();


        return response()->json(['message' => 'success'],200);
    }
    public function bannerStatus(Request $request,$idu)
    {
        $banner = bannerImage::find($request->id);

        if (!$banner) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        if ($banner->status === 'active') {
            $banner->status = 'inactive';
        } else {
            $banner->status = 'active';
        }
        $user_id = $idu ?? 1;
        $user_activity = new user_activity;
        $user_activity->user_id = $user_id;
        $user_activity-> activity_type = 'Updated banner status to ' . $banner->status;
        $user_activity ->  activity_time = now();
        $user_activity->save();

        $banner->save();

        return response()->json(['message' => 'success']);
    }
    
}
