<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\subcategory;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateStatusController extends Controller
{
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
}
