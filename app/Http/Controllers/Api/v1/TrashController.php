<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function getTrashedPosts()
{
    $trashedPosts = Post::onlyTrashed()
        ->select('posts.id',  'title', 'posts.short_desc', 'content', 'categories.name as category_name', 'categories.parent_id', 'serial_number', 'Issuance_date', 'posts.category_id', 'posts.created_at', 'posts.updated_at', 'images', 'posts.status', 'posts.views', 'file', 'parent.name as parent_name')
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
        ->get();

    return response()->json(['message' => 'success', 'data' => $trashedPosts]);
}


    public function restoreTrashedPost($id)
    {
        $trashedPost = Post::onlyTrashed()->find($id);

        if (!$trashedPost) {
            return response()->json(['message' => 'Bài viết không tìm thấy trong thùng rác'], 404);
        }
        $trashedPost->restore();

        return response()->json(['message' => 'success']);
    }
    public function getTrashedUser()
    {
        $trashedUser = User::onlyTrashed()->get();

        return response()->json(['message' => 'success','data' => $trashedUser]);
    }
    public function restoreTrashedUser($id)
    {
        $trashedUser = User::onlyTrashed()->find($id);

        if (!$trashedUser) {
            return response()->json(['message' => 'người dùng này không tìm thấy trong thùng rác'], 404);
        }
        $trashedUser->restore();

        return response()->json(['message' => 'success']);
    }
}
