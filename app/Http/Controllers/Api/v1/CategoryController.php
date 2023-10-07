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
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:categories',
        ]);

        $category = new Category;
        $category->name = $validatedData['name'];
        $category->save();

        return response()->json(['message' => 'Danh mục đã được tạo thành công!', 'category' => $category], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }

        // Lấy danh sách bài viết thuộc danh mục
        $posts = $category->posts;

        return response()->json(['category' => $category, 'posts' => $posts], 200);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }

        // Xóa liên kết bài viết thuộc danh mục
        Category_post::where('category_id', $id)->delete();

        $category->delete();

        return response()->json(['message' => 'Danh mục đã được xóa thành công'], 204);
    }
}
