<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\subcategory;
class SubCategoryController extends Controller
{
    /**
     * Hiển thị danh sách các mục con (subcategories).
     */
    public function index()
    {
        $subcategories = Subcategory::select('subcategories.id', 'subcategories.name as subcategory_name', 'categories.name as category_name', 'subcategories.created_at', 'subcategories.updated_at')
        ->join('categories', 'subcategories.category_id', '=', 'categories.id')
        ->get();

        return response()->json(['message'=>'success','data' => $subcategories]);
    }

    /**
     * Lưu một mục con (subcategory) mới vào cơ sở dữ liệu.
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
     * Hiển thị thông tin một mục con cụ thể.
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
     * Cập nhật thông tin một mục con.
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
     * Xóa một mục con khỏi cơ sở dữ liệu.
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
    public function getSubcategoriesByCategoryId(Request $request, $category_id)
    {
        $subcategories = subcategory::where('category_id', $category_id)->get();

        return response()->json($subcategories);
    }

}
