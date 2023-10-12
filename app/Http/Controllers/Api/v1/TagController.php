<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return response()->json(['data' => $tags]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:tags',
        ]);

        $tag = new Tag();
        $tag->name = $request->input('name');
        $tag->save();

        return response()->json(['message' => 'Tag created successfully', 'data' => $tag], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        return response()->json(['data' => $tag]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:tags,name,' . $id,
        ]);

        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $tag->name = $request->input('name');
        $tag->save();

        return response()->json(['message' => 'Tag updated successfully', 'data' => $tag]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }
    public function uploadImage(Request $request)
    {
        $data = $request->json()->all();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('avatars', $imageName);
            return response()->json(['message'=>'đã chạy vào đây','data'=> $imageName]);
        }
    }

}
