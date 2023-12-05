<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post; // Import model Post
use App\Models\post_history;
use App\Models\user_activity;

class PostController extends Controller
{
    /**
     * @OA\Info(
     *   title="Post API Documentation",
     *   version="1.0.0"
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/v1/post",
     *     summary="Lấy danh sách bài viết",
     *     operationId="getPosts",
     *     tags={"Posts"},
     *     @OA\Response(response=200, description="Danh sách các bài viết"),
     * )
     */
    public function index()
    {
        $posts = Post::select('posts.id',  'title', 'posts.short_desc', 'content', 'categories.name as category_name', 'categories.parent_id', 'serial_number', 'Issuance_date', 'posts.category_id', 'posts.created_at', 'posts.updated_at', 'images', 'posts.status','posts.views', 'file', 'parent.name as parent_name')
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
        ->get();

        return response()->json(['message' => 'success', 'data' => $posts], 200);

    }
    /**
     * @OA\Post(
     *     path="/api/v1/post",
     *     summary="Tạo bài viết mới",
     *     operationId="createPost",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="serial_number", type="integer"),
     *             @OA\Property(property="Issuance_date", type="string", format="date"),
     *             @OA\Property(property="subcategory_id", type="integer"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Bài viết mới đã được tạo"),
     * )
     */
    public function store(Request $request)
    {
        $post = new Post;
        $post->title = $request->input('title');
        $post->short_desc = $request->input('short_desc');
        $post->content = $request->input('content');
        $post->images = $request->input('image');

        if ($request->has('serial_number')) {
            $post->serial_number = $request->input('serial_number');
        }

        if ($request->has('Issuance_date')) {
            $post->Issuance_date = $request->input('Issuance_date');
        }

        $post->category_id = $request->input('category_id');
        $post->user_id = $request->input('user_id');

        $files = $request->input('file');
        $post->file = $files;

        $post->save();

        $userActivity = new user_activity();
        $userActivity->user_id = $request->input('user_id');
        $userActivity->activity_type = 'created post';
        $userActivity->activity_time = now();
        $userActivity->save();

        return response()->json(['message' => 'success', 'post' => $post], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/post/{id}",
     *     summary="Lấy thông tin bài viết",
     *     operationId="getPost",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của bài viết",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Thông tin bài viết"),
     *     @OA\Response(response=404, description="Không tìm thấy bài viết"),
     * )
     */
    public function show(string $id)
    {
        $post = Post::select('posts.id', 'title', 'posts.short_desc', 'content', 'categories.name as category_name', 'serial_number','file', 'Issuance_date', 'posts.created_at', 'posts.updated_at', 'images', 'status', 'posts.views',)
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        ->where('posts.id', $id)
            ->first(); 
        if (!$post) {
            return response()->json(['message' => 'error'], 404);
        }

        return response()->json(['message' => 'success', 'data' => $post], 200);
    }


    /**
     * @OA\Put(
     *     path="/api/v1/post/{id}",
     *     summary="Cập nhật bài viết",
     *     operationId="updatePost",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của bài viết",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Bài viết đã được cập nhật"),
     *     @OA\Response(response=404, description="Không tìm thấy bài viết"),
     * )
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }
        $post->title = $request->input('title');
        $post->short_desc = $request->input('short_desc');
        $post->content = $request->input('content');
        $post->images = $request->input('image');

        if ($request->has('serial_number')) {
            $post->serial_number = $request->input('serial_number');
        }

        if ($request->has('Issuance_date')) {
            $post->Issuance_date = $request->input('Issuance_date');
        }
        $oldFiles = Post::findOrFail($id)->files;
        $files = $request->input('file');
        $post->file = $files;

        if ($oldFiles) {
                $filesToDelete = array_diff($oldFiles, $files);
            } else {
                $filesToDelete = null;
            }
        if ($filesToDelete !== null) {
            foreach ($filesToDelete as $fileToDelete) {
                if (file_exists(public_path($fileToDelete))) {
                    unlink(public_path($fileToDelete));
                }
            }
        }
        $previousData = $post->toArray();
        $userActivity = new user_activity();
        $userActivity->user_id = $request->input('user_id');
        $userActivity->activity_type = 'updated post';
        $userActivity->activity_time = now();
        $userActivity->save();

        $postHistory = new post_history();
        $postHistory->post_id = $post->id;
        $postHistory->user_id = $request->input('user_id');
        $postHistory->previous_data = json_encode($previousData);
        $postHistory->updated_data = json_encode([$post]);
        $postHistory->action = 'updated post';
        $postHistory->action_time=now();
        $postHistory->save();

        $post->save();

        return response()->json(['message' => 'success', 'post' => $post], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/post/{id}",
     *     summary="Xóa bài viết",
     *     operationId="deletePost",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của bài viết",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=204, description="Bài viết đã được xóa thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy bài viết"),
     * )
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'error'], 404);
        }
        $postHistory = new post_history();
        $postHistory->post_id = $post->id;
        $postHistory->user_id = $post->user_id;
        $postHistory->previous_data = json_encode($post->toArray());
        $postHistory->action = 'deleted post';
        $postHistory->save();

        $userActivity = new user_activity();
        $userActivity->user_id = $post->user_id;
        $userActivity->activity_type = 'deleted post';
        $userActivity->activity_time = now();
        $userActivity->save();

        $post->delete();

        return response()->json(['message' => 'success']);
    }

    public function uploadPostFile(Request $request)
    {
        $uploadPath = public_path('uploads/filePost');

        if ($request->hasFile('files')) {
            $uploadedFile = $request->file('files');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $newFileName = "{$fileName}.{$extension}";
            $uploadedFile->move($uploadPath, $originalName);
            $filesUrl = '/uploads/filePost/' . $newFileName;

            return response()->json(['message' => 'success', 'data' => $filesUrl]);
        }
        return response()->json(['message' => 'No valid files to upload', 'data' => []], 400);
    }
    public function getAllbyCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $posts = Post::select('posts.id', 'title', 'posts.short_desc', 'content', 'categories.name as category_name', 'serial_number', 'Issuance_date', 'posts.created_at', 'posts.updated_at', 'file', 'images', 'status')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->where(function ($query) use ($category) {
                $query->where('posts.category_id', $category->id)
                    ->orWhere('categories.parent_id', $category->id);
            })
            ->get();

        return response()->json(['message' => 'success', 'data' => $posts], 200);
    }


}
