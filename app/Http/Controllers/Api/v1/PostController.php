<?php

namespace App\Http\Controllers\Api\v1;

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
        $posts = Post::select('posts.id','title','content', 'categories.name as category_name',  'serial_number', 'Issuance_date', 'posts.created_at', 'posts.updated_at', 'images', 'posts.status')
        ->join('categories', 'posts.category_id', '=', 'categories.id')
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
        $post->content = $request->input('content');
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('/uploads/postImage/'), $imageName);
            $post->images = '/uploads/postImage/' . $imageName;
        }
        if ($request->has('serial_number')) {
            $post->serial_number = $request->input('serial_number');
        }
        if ($request->has('Issuance_date')) {
            $post->Issuance_date = $request->input('Issuance_date');
        }
        $post->subcategory_id = $request->input('subcategory_id');
        $post->user_id = $request->input('user_id');

        $post->save();
        $userActivity = new user_activity();
        $userActivity->user_id = $request->input('user_id');
        $userActivity->activity = 'created';
        $userActivity->activity_type = 'post';
        $userActivity->activity_id = $post->id;
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
        $posts = Post::select('posts.id', 'title', 'content', 'categories.name as category_name', 'serial_number', 'Issuance_date', 'posts.created_at', 'posts.updated_at', 'images', 'status')
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        ->where('categories.id', $id)
            ->get();
        if (!$posts) {
            return response()->json(['message' => 'error'], 404);
        }

        return response()->json(['message' => 'success', 'data' => $posts], 200);
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
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('/uploads/postImage/'), $imageName);
            $post->images = '/uploads/postImage/' . $imageName;
        }
        if ($request->has('serial_number')) {
            $post->serial_number = $request->input('serial_number');
        }
        if ($request->has('Issuance_date')) {
            $post->Issuance_date = $request->input('Issuance_date');
        }
        $post->subcategory_id = $request->input('subcategory_id');
        $post->user_id = $request->input('user_id');
        $post->save();
        $previousData = $post->toArray();
        $userActivity = new user_activity();
        $userActivity->user_id = $request->input('user_id');
        $userActivity->activity = 'updated';
        $userActivity->activity_type = 'post';
        $userActivity->activity_id = $post->id;
        $userActivity->save();

        // Lưu lịch sử bài viết
        $postHistory = new post_history();
        $postHistory->post_id = $post->id;
        $postHistory->user_id = $request->input('user_id');
        $postHistory->previous_data = json_encode($previousData);
        $postHistory->updated_data = json_encode($post->toArray());
        $postHistory->save();

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
        $postHistory->deleted_at = now();
        $postHistory->save();

        // Ghi lịch sử hoạt động của người dùng
        $userActivity = new user_activity();
        $userActivity->user_id = $post->user_id;
        $userActivity->activity = 'deleted';
        $userActivity->activity_type = 'post';
        $userActivity->activity_id = $post->id;
        $userActivity->save();

        $post->delete();

        return response()->json(['message' => 'success']);
    }
}
