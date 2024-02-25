<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::select('categories.id', 'categories.name','parent.name as parent_name', 'parent.id as parent_id', 'categories.created_at', 'categories.updated_at')
        ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
        ->get(); 
        $categories = $categories->map(function ($category) {
            if ($category->parent_name === 0) {
                $category->parent_name = '';
            }
            return $category;
        });
        $categories_data = Category::all();
        return response()->json(['message' => 'success', 'data' => $categories,'category' => $categories_data]);
    }
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(['message' => 'success', 'data' => $category]);
    }
   
    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->input('name');
        if($request->has('parent_id')){
            $category->parent_id = $request->input('parent_id');
        }
        $category->save();

        return response()->json(['message' => 'success','data'=>$category], 201);
    }
   
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required'
        ]);
        
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->name = $request->input('name');
        $category->parent_id = $request->input('parent_id');

        $category->save();

        return response()->json(['message' => 'success','data'=>$category]);
    }

    public function updatecategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required'
        ]);
        
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->name = $request->input('name');
        $category->parent_id = $request->input('parent_id');
        $category->save();

        return response()->json(['message' => 'success','data'=>$category]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message'=>'success'], 204);
    }
    public function getParentCategory()
    {
        $categories = Category::where('parent_id', 0)->get();
        foreach ($categories as $category) {
            $category->subcategories = Category::where('parent_id', $category->id)->get();
        }
        return response()->json(['message' => 'success','data' => $categories]);
    }

    public function getSubCategory($id)
    {
        $subcategories = Category::where('parent_id', $id)->get();
        return response()->json(['message' => 'success','data' => $subcategories]);
    }

    public function getAllCategoriesWithSubcategories()
    {
        $categories = Category::where('parent_id', 0)->get();
        foreach ($categories as $category) {
            $category->subcategories = Category::where('parent_id', $category->id)->get();
        }
        return response()->json(['message' => 'success','data' => $categories]);
    }
    public function getIntroductionChild(){
        $name = 'Giới thiệu';
        $categoryIds = Category::where('name', $name)->orWhere('parent_id', function ($query) use ($name) {
            $query->select('id')->from('categories')->where('name', $name);
        })->get();
        
        return response()->json(['data' => $categoryIds],200);
    }
}
