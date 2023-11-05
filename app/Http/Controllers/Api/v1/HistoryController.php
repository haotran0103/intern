<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use App\Models\post_history;
use App\Models\user_activity;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function post_history(){
        $post_history = post_history::select('post_histories.id', 'posts.title as post_title', 'users.name as user_name', 'previous_data', 'updated_data', 'action', 'action_time')
        ->join('posts', 'post_histories.post_id', '=', 'posts.id')
        ->join('users', 'post_histories.user_id', '=', 'users.id')
        ->get();
        return response()->json(['message' => 'success', 'data' => $post_history], 200);
    }
    public function user_history()
    {
        $user_history = user_activity::select('ua.id', 'u.name as user_name', 'ua.activity_type', 'ua.activity_time')
            ->from('user_activities as ua')
            ->join('users as u', 'ua.user_id', '=', 'u.id')
            ->get();

        return response()->json(['message' => 'success', 'data' => $user_history], 200);
    }


}
