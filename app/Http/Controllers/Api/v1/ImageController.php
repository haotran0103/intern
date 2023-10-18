<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * @OA\Info(
     *   title="Image Upload API Documentation",
     *   version="1.0.0"
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/v1/upload-image",
     *     summary="Tải lên hình ảnh",
     *     operationId="uploadImage",
     *     tags={"Images"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Hình ảnh đã được tải lên thành công"),
     *     @OA\Response(response=400, description="Không có tệp nào được tải lên.")
     * )
     */
    public function uploadImage(Request $request)
    {
        $uploadedImage = $request->file('upload');

        if ($uploadedImage) {
            $imageName = time() . '.' . $uploadedImage->getClientOriginalExtension();
            $uploadedImage->move(public_path('/uploads/post/'), $imageName);
            $imageUrl = '/uploads/post/' . $imageName;

            return response()->json(['url' => asset($imageUrl),'uploaded'=>true],200);
        }

        return response()->json(['error' => 'Không có tệp nào được tải lên.'], 400);
    }

}
