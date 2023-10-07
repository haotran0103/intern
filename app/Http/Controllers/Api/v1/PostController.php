<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Post_Tag;
use App\Models\Tag;
use Google\Cloud\Core\ExponentialBackoff;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post; // Import model Post

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('tags')->get();

        return response()->json($posts, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|string',
            'tags' => 'nullable|array', 
        ]);

        $post = new Post;
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        $post->image = $validatedData['image'];
        $post->user_id = auth()->user()->id;
        $post->save();

        if (isset($validatedData['tags'])) {
            $tags = $validatedData['tags'];

            foreach ($tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]); 
                Post_Tag::create(['post_id' => $post->id, 'tag_id' => $tag->id]);
            }
        }

        return response()->json(['message' => 'Bài viết đã được tạo thành công!', 'post' => $post], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $tags = $post->tags;

        return response()->json(['post' => $post, 'tags' => $tags], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'string',
            'content' => 'string',
            'image' => 'string',
            'user_id' => 'integer',
            'tags' => 'nullable|array', 
        ]);

        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $post->update($validatedData);

        if (isset($validatedData['tags'])) {
            $tags = $validatedData['tags'];

            Post_Tag::where('post_id', $post->id)->delete();

            foreach ($tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]); 
                Post_Tag::create(['post_id' => $post->id, 'tag_id' => $tag->id]);
            }
        }

        return response()->json(['message' => 'Bài viết đã được cập nhật!', 'post' => $post], 200);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id); 
        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Bài viết đã được xóa thành công'], 204);
    }
}
