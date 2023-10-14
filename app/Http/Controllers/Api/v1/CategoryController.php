<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Category_post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(['data'=> $categories,'message'=>'success'], 200);
    }

    public function store(Request $request)
    {

        $category = new Category;
        $category->name = $request->input('name');
        $category->save();

        return response()->json(['message' => 'success', 'category' => $category], 201);
    }


    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }

        $posts = $category->posts;

        return response()->json(['category' => $category, 'posts' => $posts], 200);
    }

/**
 * The function updates a category's name in the database based on the provided request data and
 * returns a JSON response with a success message and the updated category.
 * 
 * @param Request request The  parameter is an instance of the Request class, which represents
 * the HTTP request made to the server. It contains information about the request, such as the request
 * method, headers, and input data.
 * @param id The  parameter is the identifier of the category that needs to be updated. It is used
 * to find the category in the database and update its name.
 * 
 * @return The code is returning a JSON response with a message and the updated category object. The
 * message indicates that the category has been successfully updated.
 */

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'string|unique:categories,name,' . $id,
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }

        $category->name = $validatedData['name'];
        $category->save();

        return response()->json(['message' => 'Danh mục đã được cập nhật!', 'category' => $category], 200);
    }


/**
 * The `destroy` function in PHP deletes a category and its associated posts from the database.
 * 
 * @param id The parameter "id" represents the ID of the category that needs to be deleted.
 * 
 * @return a JSON response with a message indicating whether the category was successfully deleted or
 * not. If the category is not found, a 404 status code and a message "Không tìm thấy danh mục"
 * (Category not found) will be returned. If the category is successfully deleted, a 204 status code
 * and a message "Danh mục đã được xóa
 */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }
        Category_post::where('category_id', $id)->delete();

        $category->delete();

        return response()->json(['message' => 'Danh mục đã được xóa thành công'], 204);
    }
}
