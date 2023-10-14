<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * The function uploads an image file to a server and returns a JSON response with the success
     * message and the URL of the uploaded image.
     * 
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request made to the server. It contains information about the request, such
     * as the request method, headers, and any data sent with the request.
     * 
     * @return a JSON response. If an image is successfully uploaded, it will return a success message
     * along with the image URL. If no image is uploaded, it will return an error message.
     */
    public function uploadImage(Request $request)
    {
        $uploadedImage = $request->file('image');

        if ($uploadedImage) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('post', $imageName);
            $imageUrl = 'post/' . $imageName;

            return response()->json(['success' => 'đã được tải lên.', 'imagesurl'=>$imageUrl], 200);
        }

        return response()->json(['error' => 'Không có tệp nào được tải lên.'], 400);
    }

}
