<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Info(
     *     title="Category API Documentation",
     *     version="1.0.0",
     * )
     */
    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="Lấy danh sách tất cả các danh mục",
     *     operationId="getCategories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Danh sách tất cả các danh mục"),
     * )
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     summary="Lấy thông tin của danh mục theo ID",
     *     operationId="getCategory",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Thông tin của danh mục"),
     *     @OA\Response(response=404, description="Không tìm thấy danh mục"),
     * )
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
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
     *             @OA\Property(property="name", type="string", description="Tên của danh mục"),
     *             @OA\Property(property="parent_id", type="integer", description="ID của danh mục cha (nếu có)"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Danh mục mới đã được tạo"),
     * )
     */
    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->input('name');
        $category->parent_id = $request->input('parent_id');
        $category->save();

        return response()->json($category, 201);
    }
    /**
     * @OA\Put(
     *     path="/api/v1/categories/{id}",
     *     summary="Cập nhật danh mục theo ID",
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
     *             @OA\Property(property="name", type="string", description="Tên của danh mục"),
     *             @OA\Property(property="parent_id", type="integer", description="ID của danh mục cha (nếu có)"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Danh mục đã được cập nhật"),
     *     @OA\Response(response=404, description="Không tìm thấy danh mục"),
     * )
     */

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->name = $request->input('name');
        $category->parent_id = $request->input('parent_id');
        $category->save();

        return response()->json($category);
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/categories/{id}",
     *     summary="Xóa danh mục theo ID",
     *     operationId="deleteCategory",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Danh mục đã được xóa"),
     *     @OA\Response(response=404, description="Không tìm thấy danh mục"),
     * )
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json([], 204);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/parent-categories",
     *     summary="Lấy danh sách các danh mục cha",
     *     operationId="getParentCategories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Danh sách các danh mục cha"),
     * )
     */
    public function getParentCategory()
    {
        $categories = Category::where('parent_id', 0)->get();
        foreach ($categories as $category) {
            $category->subcategories = Category::where('parent_id', $category->id)->get();
        }
        return response()->json(['data' => $categories]);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/sub-categories/{id}",
     *     summary="Lấy danh sách các danh mục con dựa trên ID danh mục cha",
     *     operationId="getSubCategories",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của danh mục cha",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Danh sách các danh mục con"),
     * )
     */
    public function getSubCategory($id)
    {
        $subcategories = Category::where('parent_id', $id)->get();
        return response()->json(['data' => $subcategories]);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/all-categories-with-subcategories",
     *     summary="Lấy danh sách tất cả các danh mục với danh mục con",
     *     operationId="getAllCategoriesWithSubcategories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Danh sách tất cả các danh mục với danh mục con"),
     * )
     */
    public function getAllCategoriesWithSubcategories()
    {
        $categories = Category::where('parent_id', 0)->get();
        foreach ($categories as $category) {
            $category->subcategories = Category::where('parent_id', $category->id)->get();
        }
        return response()->json(['data' => $categories]);
    }

}
