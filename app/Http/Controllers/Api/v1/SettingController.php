<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\post_history;
use App\Models\user_activity;
use App\Models\setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class SettingController extends Controller
{
    public function ReadSetting()
    {
        $setting = Setting::all();
        $path = public_path().'/json/';
        if(!is_dir($path)){
            mkdir($path,0777, true);
        }
        File::put($path.'Setting.json',json_encode(Setting::all()));
        return response()->json($setting, 200);
    }
    public function AddSetting(Request $request)
    {
        $setting = new Setting();
        $setting->config_key = $request->input('config_key');
        $type = $request->input('add-type');
        if ($type == 'image') {

            $image = $request->file('config_value');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banner'), $imageName);

            $setting->config_value = 'uploads/banner/' . $imageName;
        } else {
            $setting->config_value = $request->input('config_value');
        }
        $setting->type = $type;
        $setting->save();
        return response()->json(['message' => 'success', 'data' => $setting], 201);
    }
    public function UpdateSetting(Request $request, $id)
    {
        $setting = Setting::find($id);
    
        if (!$setting) {
            return response()->json(['message' => 'Setting not found'], 404);
        }
    
        $setting->config_key = $request->input('config_key');
        $type = $request->input('edit-type');
    
        // Handle image logic
        if ($type == 'image') {
            $image = $request->file('config_value');
    
            if ($image) {
                // If a new image is provided, delete the old image and update with the new one
                if ($setting->config_value) {
                    // Delete the old image
                    $oldImagePath = public_path($setting->config_value);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
    
                // Move the new image to the 'uploads/banner' directory
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/banner'), $imageName);
                $setting->config_value = 'uploads/banner/' . $imageName;
            }
        } else {
            // For text type or if no new image is provided, update the text value
            $setting->config_value = $request->input('config_value');
        }
    
        $setting->type = $type;
        $setting->save();
    
        return response()->json(['message' => 'success', 'data' => $setting], 200);
    }
    

    public function post_history()
    {
        $post_history = post_history::all();

        return response()->json(['message' => 'success', 'data' => $post_history], 200);
    }
    public function user_activities()
    {
        $user_activities = user_activity::all();

        return response()->json(['message' => 'success', 'data' => $user_activities], 200);
    }
}
