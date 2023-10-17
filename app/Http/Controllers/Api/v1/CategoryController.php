<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Category_post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Info(
     *     title="category API Documentation",
     *     version="1.0.0",
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="Lấy danh sách các danh mục",
     *     operationId="getCategories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Danh sách các danh mục"),
     * )
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(['data'=> $categories,'message'=>'success'], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/v1/categories",
     *     summary="Tạo danh mục mới",
     *     operationId="createCategory",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Danh mục mới")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Danh mục mới đã được tạo"),
     * )
     */
    public function store(Request $request)
    {

        $category = new Category;
        $category->name = $request->input('name');
        $category->save();

        return response()->json(['message' => 'success', 'category' => $category], 201);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     summary="Lấy thông tin danh mục",
     *     operationId="getCategory",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Thông tin danh mục"),
     *     @OA\Response(response=404, description="Không tìm thấy danh mục"),
     * )
     */

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
     * @OA\Put(
     *     path="/api/v1/categories/{id}",
     *     summary="Cập nhật danh mục",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Danh mục cập nhật")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Danh mục đã được cập nhật"),
     *     @OA\Response(response=404, description="Không tìm thấy danh mục"),
     * )
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
     * @OA\Delete(
     *     path="/api/v1/categories/{id}",
     *     summary="Xóa danh mục",
     *     operationId="deleteCategory",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Danh mục đã được xóa thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy danh mục"),
     * )
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
