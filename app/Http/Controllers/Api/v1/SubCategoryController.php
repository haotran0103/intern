<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\subcategory;
class SubCategoryController extends Controller
{
    /**
     * @OA\Info(
     *   title="Subcategory API Documentation",
     *   version="1.0.0"
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/v1/subcategories",
     *     summary="Hiển thị danh sách các mục con (subcategories)",
     *     operationId="getSubcategories",
     *     tags={"Subcategories"},
     *     @OA\Response(response=200, description="Danh sách các mục con (subcategories)"),
     * )
     */
    public function index()
    {
        $subcategories = Subcategory::select('subcategories.id', 'subcategories.name as subcategory_name', 'categories.name as category_name', 'subcategories.created_at', 'subcategories.updated_at')
        ->join('categories', 'subcategories.category_id', '=', 'categories.id')
        ->get();

        return response()->json(['message'=>'success','data' => $subcategories]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/subcategories",
     *     summary="Lưu một mục con (subcategory) mới vào cơ sở dữ liệu",
     *     operationId="createSubcategory",
     *     tags={"Subcategories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="category_id", type="integer"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Mục con (subcategory) mới đã được tạo"),
     * )
     */
    public function store(Request $request)
    {
        $subcategory = new subcategory();

        $subcategory->name = $request->input('name');
        $subcategory->category_id = $request->input('category_id');
        
        $subcategory->save();
        return response()->json(['message' => 'success', 'data' => $subcategory], 201);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/subcategories/{id}",
     *     summary="Hiển thị thông tin một mục con cụ thể",
     *     operationId="showSubcategory",
     *     tags={"Subcategories"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID của mục con", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Thông tin mục con"),
     *     @OA\Response(response=404, description="Không tìm thấy mục con"),
     * )
     */
    public function show(string $id)
    {
        $subcategory = subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Không tìm thấy mục con'], 404);
        }

        return response()->json(['data' => $subcategory]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/subcategories/{id}",
     *     summary="Cập nhật thông tin một mục con",
     *     operationId="updateSubcategory",
     *     tags={"Subcategories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của mục con (subcategory)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="category_id", type="integer"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Thông tin mục con đã được cập nhật"),
     *     @OA\Response(response=404, description="Không tìm thấy mục con"),
     * )
     */
    public function update(Request $request, string $id)
    {
        $subcategory = subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Không tìm thấy mục con'], 404);
        }

        $data = $request->validate([
            'name' => 'string',
            'category_id' => 'integer',
        ]);

        $subcategory->update($data);

        return response()->json(['message' => 'Thông tin mục con đã được cập nhật', 'data' => $subcategory]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/subcategories/{id}",
     *     summary="Xóa một mục con khỏi cơ sở dữ liệu",
     *     operationId="deleteSubcategory",
     *     tags={"Subcategories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của mục con (subcategory)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Mục con đã bị xóa"),
     *     @OA\Response(response=404, description="Không tìm thấy mục con"),
     * )
     */
    public function destroy(string $id)
    {
        $subcategory = subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Không tìm thấy mục con'], 404);
        }

        $subcategory->delete();

        return response()->json(['message' => 'Mục con đã bị xóa']);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/subcategories/{category_id}",
     *     summary="Lấy danh sách mục con theo ID của danh mục cha",
     *     operationId="getSubcategoriesByCategoryId",
     *     tags={"Subcategories"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="path",
     *         required=true,
     *         description="ID của danh mục cha",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Danh sách các mục con theo danh mục cha"),
     * )
     */
    public function getSubcategoriesByCategoryId(Request $request, $category_id)
    {
        $subcategories = subcategory::where('category_id', $category_id)->get();

        return response()->json($subcategories);
    }

}
