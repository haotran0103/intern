<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\banner_image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class BannerImagesController extends Controller
{
    /**
     * Hiển thị danh sách các hình ảnh banner.
     */
    public function index()
    {
        $bannerImages = banner_image::where('status', 'active')->limit(5)->get();
        return response()->json(['message'=>'success','data'=> $bannerImages]);
    }

    /**
     * Hiển thị một hình ảnh cụ thể theo ID.
     */
    public function show($id)
    {
        $bannerImage = banner_image::find($id);
        if (!$bannerImage) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }
        return response()->json(['message'=>'success','data'=>$bannerImage]);
    }

    /**
     * Lưu một hình ảnh banner mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        $Banner_image = new banner_image;
        $Banner_image->image_url = $request->input('images');
        $Banner_image->status = $request->input('status');
        $Banner_image->save();
    }

    /**
     * Cập nhật thông tin hình ảnh banner theo ID.
     */
    public function update(Request $request, $id)
    {
        $bannerImage = banner_image::find($id);
        if (!$bannerImage) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }

        $request->validate([
            'image_url' => 'required',
            'status' => 'in:active,inactive'
        ]);

        $bannerImage->update($request->all());
        return response()->json($bannerImage);
    }

    /**
     * Xóa một hình ảnh banner theo ID.
     */
    public function destroy($id)
    {
        $bannerImage = banner_image::find($id);
        if (!$bannerImage) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }

        $bannerImage->delete();
        return response()->json(['message' => 'success']);
    }
    public function getAll(){
        $bannerImages = banner_image::all();
        return response()->json(['message' => 'success', 'data' => $bannerImages]);
    }

}
