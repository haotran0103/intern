<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\bannerImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class BannerImagesController extends Controller
{
    /**
     * Hiển thị danh sách các hình ảnh banner.
     */
    public function index()
    {
        $bannerImages = bannerImage::where('status', 'active')->limit(5)->get();
        return response()->json(['message'=>'success','data'=> $bannerImages]);
    }

    /**
     * Hiển thị một hình ảnh cụ thể theo ID.
     */
    public function show($id)
    {
        $bannerImage = bannerImage::find($id);
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
        $bannerImage = new bannerImage;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banner'), $imageName); 
            $bannerImage->image_url = 'uploads/banner/' . $imageName; 
        }
        $bannerImage->status = $request->input('status');
        $bannerImage->save();
    }

    /**
     * Cập nhật thông tin hình ảnh banner theo ID.
     */
    public function update(Request $request, $id)
    {
        $bannerImage = bannerImage::find($id);
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
        $bannerImage = bannerImage::find($id);
        if (!$bannerImage) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }
        if ($bannerImage->image_url) {
            $oldImagePath = public_path($bannerImage->image_url);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        $bannerImage->delete();
        return response()->json(['message' => 'success']);
    }
    public function getAll(){
        $bannerImages = bannerImage::all();
        return response()->json(['message' => 'success', 'data' => $bannerImages]);
    }

}
