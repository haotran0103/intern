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

    public function userStatus(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        if ($user->status === 'active') {
            $user->status = 'deactivated';
        } else {
            $user->status = 'active';
        }

        $user->save();

        return response()->json(['message' => 'success']);
    }


    public function postStatus(Request $request)
    {
        $post = Post::find($request->id);

        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $oldPostData = $post->toArray();

        if ($post->status === 'active') {
            $post->status = 'deactivated';
        } else {
            $post->status = 'active';
        }

        $post->save();

        return response()->json(['message' => 'success'],200);
    }
    public function bannerStatus(Request $request,$idu)
    {
        $banner = bannerImage::find($request->id);

        if (!$banner) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        if ($banner->status === 'active') {
            $banner->status = 'deactivated';
        } else {
            $banner->status = 'active';
        }

        $banner->save();

        return response()->json(['message' => 'success']);
    }
    
}
