<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\post_history;
use App\Models\user_activity;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function ReadSetting(){
        $jsonFilePath = public_path('setting/config.json');
        $jsonData = json_decode(file_get_contents($jsonFilePath), true);
        return response()->json($jsonData, 200);
    }
    public function UpdateSetting(Request $request)
    {
        $jsonFilePath = public_path('setting/config.json');
        if (file_exists($jsonFilePath)) {
            $jsonData = json_decode(file_get_contents($jsonFilePath), true);
            $newData = $request->all();
            $updatedJson = json_encode($newData);
            file_put_contents($jsonFilePath, $updatedJson);

            return response()->json(['message' => 'success'], 200);
        } else {
            return response()->json(['error' => 'Tệp không tồn tại'], 404);
        }
    }
    public function post_history(){
        $post_history = post_history::all();

        return response()->json(['message' => 'success','data'=> $post_history], 200);
    }
    public function user_activities()
    {
        $user_activities = user_activity::all();

        return response()->json(['message' => 'success', 'data' => $user_activities], 200);
    }

}
