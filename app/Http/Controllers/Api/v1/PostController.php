<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post; // Import model Post
use Illuminate\Database\Eloquent\Builder;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\File;
class PostController extends Controller
{

    public function index()
    {
        $posts = Post::select(
            'posts.id',
            'title',
            'posts.short_desc',
            'content',
            'categories.name as category_name',
            'categories.parent_id',
            'serial_number',
            'Issuance_date',
            'posts.category_id',
            'posts.created_at',
            'posts.updated_at',
            'images',
            'posts.status',
            'posts.views',
            'file',
            'parent.name as parent_name'
        )
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
            ->get();
        $path = public_path().'/json/';
        if(!is_dir($path)){
            mkdir($path,0777, true);
        }
        File::put($path.'post.json',json_encode(Post::all()));
        $postsWithAllCategories = $posts->map(function ($post) {
            $categories = $this->getAllCategories($post->category_id);
            return array_merge($post->toArray(), ['all_categories' => $categories]);
        });

        return response()->json(['message' => 'success', 'data' => $postsWithAllCategories], 200);
    }

    protected function getAllCategories($categoryId)
    {
        $categories = Category::where('id', $categoryId)->get();

        $categories->each(function ($category) use (&$categories) {
            $parentCategory = Category::find($category->parent_id);
            if ($parentCategory) {
                $parentCategories = $this->getAllCategories($parentCategory->id);
                $categories = $categories->concat($parentCategories);
            }
        });

        return $categories;
    }

    public function store(Request $request)
    {

        $post = new Post();
        $post->title = $request->input('title');
        $post->short_desc = $request->input('short_desc');
        if ($request->has('content')) {
            $post->content = $request->input('content');
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/postImages'), $imageName); // Lưu hình ảnh vào thư mục public/images

            $post->images = 'uploads/postImages/' . $imageName; // Lưu đường dẫn của hình ảnh vào cơ sở dữ liệu
        } else {
            $post->images = 'imgs/documentImages.jpg';
        }

        if ($request->has('serial_number')) {
            $post->serial_number = $request->input('serial_number');
        }

        if ($request->has('Issuance_date')) {
            $post->Issuance_date = $request->input('Issuance_date');
        }

        $post->category_id = $request->input('category_id');

        $post->user_id = JWTAuth::user()->id;

        if ($request->hasFile('file')) {
            $files = $request->file('file');
            $filePaths = [];

            foreach ($files as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/postFiles'), $fileName);
                $filePaths[] = 'uploads/postFiles/' . $fileName;
            }

            $post->file = json_encode($filePaths); // Lưu danh sách đường dẫn file vào cơ sở dữ liệu
        }

        $post->save();

        return response()->json(['message' => 'success', 'post' => $post], 201);
    }
    public function show(string $id)
    {
        $post = Post::select('posts.id', 'title', 'posts.short_desc', 'content', 'categories.name as category_name', 'serial_number', 'file', 'Issuance_date', 'posts.created_at', 'posts.updated_at', 'images', 'status', 'posts.views',)
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->where('posts.id', $id)
            ->first();
        if (!$post) {
            return response()->json(['message' => 'error'], 404);
        }

        return response()->json(['message' => 'success', 'data' => $post], 200);
    }

    public function update(Request $request)
    {
        $post = Post::find($request->postID);
        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $post->title = $request->input('title');
        $post->short_desc = $request->input('short_desc');
        if ($request->has('content')) {
            $post->content = $request->input('content');
        }
        $post->category_id = $request->input('category_id');
        $post->user_id = auth()->user()->id;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/postImages'), $imageName);
            if ($post->images) {
                $oldImagePath = public_path($post->images);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $post->images = 'uploads/postImages/' . $imageName;
        }

        if ($request->has('serial_number')) {
            $post->serial_number = $request->input('serial_number');
        }
        if ($request->has('Issuance_date')) {
            $post->Issuance_date = $request->input('Issuance_date');
        }

        if ($request->hasFile('file')) {
            $files = $request->file('file');
            $filePaths = [];

            foreach ($files as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/postFiles'), $fileName);
                $filePaths[] = 'uploads/postFiles/' . $fileName;
            }
            if ($post->file) {
                $oldFilePaths = json_decode($post->file, true);
                foreach ($oldFilePaths as $oldFilePath) {
                    $oldFileFullPath = public_path($oldFilePath);
                    if (file_exists($oldFileFullPath)) {
                        unlink($oldFileFullPath);
                    }
                }
            }

            $post->file = json_encode($filePaths);
        }

        $post->save();

        return response()->json(['message' => 'success', 'post' => $post], 200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'error'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'success'], 200);
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
            $filesUrl = 'uploads/filePost/' . $newFileName;

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
    public function detail($id)
    {
        $post = Post::select(
            'posts.id',
            'title',
            'posts.short_desc',
            'content',
            'categories.name as category_name',
            'categories.parent_id',
            'serial_number',
            'Issuance_date',
            'posts.category_id',
            'posts.created_at',
            'posts.updated_at',
            'images',
            'posts.status',
            'posts.views',
            'file',
            'parent.name as parent_name'
        )
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
            ->where('posts.id', $id)
            ->first();

        if (!$post) {
            return redirect()->back()->with('error', 'Không tìm thấy bài viết.');
        }

        // Lấy danh sách các thể loại con của thể loại cha của bài viết hiện tại
        $childCategories = Category::where('parent_id', $post->category_id)->pluck('id')->toArray();

        // Lấy tất cả các bài viết thuộc các thể loại con này (không bao gồm bài viết hiện tại)
        $relatedPosts = Post::where('category_id', $post->category_id)
            ->orWhereIn('category_id', $childCategories)
            ->where('id', '!=', $id)
            ->get();

        $allCategories = $this->getAllCategories($post->category_id);

        return view('client.news.detail.index')
            ->with('post', $post)
            ->with('relatedPosts', $relatedPosts)
            ->with('allCategories', $allCategories);
    }
    public function allPost()
    {
        // Lấy tất cả bài viết
        $posts = Post::select(
            'posts.id',
            'title',
            'posts.short_desc',
            'content',
            'categories.name as category_name',
            'categories.parent_id',
            'serial_number',
            'Issuance_date',
            'posts.category_id',
            'posts.created_at',
            'posts.updated_at',
            'images',
            'posts.status',
            'posts.views',
            'file',
            'parent.name as parent_name'
        )
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
            ->where('categories.name', '!=', 'Video')
            ->where('categories.name', '!=', 'Tin tức')
            ->paginate(10);

        if (!$posts) {
            return redirect()->back()->with('error', 'Không tìm thấy bài viết.');
        }

        $categoriesWithChildren = $this->categoryRecusive();
        return view('client.news.index')
            ->with('posts', $posts)
            ->with('categoriesWithChildren', $categoriesWithChildren);
    }

    public function allPostCategory($id)
    {
        // Lấy tất cả các thể loại con của thể loại được chỉ định
        $categoryIds = Category::where('id', $id)->orWhere('parent_id', $id)->pluck('id')->toArray();
    
        // Lấy tất cả bài viết thuộc các thể loại đã được lấy
        $posts = Post::select(
            'posts.id',
            'title',
            'posts.short_desc',
            'content',
            'categories.name as category_name',
            'categories.parent_id',
            'serial_number',
            'Issuance_date',
            'posts.category_id',
            'posts.created_at',
            'posts.updated_at',
            'images',
            'posts.status',
            'posts.views',
            'file',
            'parent.name as parent_name'
        )
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
            ->whereIn('posts.category_id', $categoryIds)
            ->paginate(10);
    
        if ($posts->isEmpty()) {
            return redirect()->back()->with('error', 'Không tìm thấy bài viết.');
        }
    
        $categoriesWithChildren = $this->categoryRecusive();
        return view('client.categoriesPage.index')
            ->with('posts', $posts)
            ->with('categoriesWithChildren', $categoriesWithChildren);
    }
  
    protected function categoryRecusive($parentId = 0)
    {
        $categories = Category::where('parent_id', $parentId)->get();
        $categoryList = [];

        foreach ($categories as $category) {
            $children = $this->categoryRecusive($category->id);
            $categoryList[] = [
                'id' => $category->id,
                'name' => $category->name,
                'children' => $children,
            ];
        }

        return $categoryList;
    }
    public function filterByCategory($categoryId)
    {
        $posts = Post::whereHas('category', function (Builder $query) use ($categoryId) {
            $query->where('id', $categoryId) // Lấy bài viết của thể loại cha
                ->orWhere('parent_id', $categoryId); // Lấy bài viết của các thể loại con
        })->paginate(10);

        $posts->each(function ($post) {
            $post->is_video = $post->category->name === 'Video';;
        });
        return response()->json($posts);
    }
    public function postIndex()
    {
        $newsCategoryId = Category::where('name', 'Tin tức')->pluck('id')->first();
        $newsCategoryIds = Category::where('parent_id', $newsCategoryId)->pluck('id');
        $newsPosts = Post::whereIn('category_id', $newsCategoryIds)->orderBy('created_at', 'desc')->take(5)->get();

        $latestPosts = Post::whereNotIn('category_id', function ($query) {
            $query->select('id')->from('categories')->whereIn('name', ['Tập tin', 'Thông báo', 'Video']);
        })->orderBy('created_at', 'desc')->take(4)->get();

        $mostPopularPosts = Post::with(['category.parent'])
            ->whereNotIn('category_id', function ($query) {
                $query->select('id')->from('categories')->whereIn('name', ['Tập tin', 'Thông báo', 'Video']);
            })
            ->orderBy('views', 'desc')
            ->take(6)
            ->get();

        $NotificationPosts = Post::whereIn('category_id', function ($query) {
            $query->select('id')->from('categories')->whereIn('name', ['Thông báo']);
        })->orderBy('views', 'desc')->take(6)->get();

        $VideoPosts = Post::whereIn('category_id', function ($query) {
            $query->select('id')->from('categories')->whereIn('name', ['Video']);
        })->orderBy('views', 'desc')->take(3)->get();

        return view('client.HomePage.index', [
            'newsPost' => $newsPosts, 'latestPosts' => $latestPosts,
            'mostPopularPosts' => $mostPopularPosts, 'NotificationPosts' => $NotificationPosts,
            'VideoPosts' => $VideoPosts
        ]);
    }
    public function getAllDocument(){
        $posts = Post::select(
            'posts.id',
            'title',
            'posts.short_desc',
            'content',
            'categories.name as category_name',
            'categories.parent_id',
            'serial_number',
            'Issuance_date',
            'posts.category_id',
            'posts.created_at',
            'posts.updated_at',
            'images',
            'posts.status',
            'posts.views',
            'file',
            'parent.name as parent_name'
        )
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
            ->whereRaw('LOWER(categories.name) = ?', ['tập tin'])
            ->paginate(10);
            
        return view('client.documents.index')->with('posts', $posts);
    }
    
}
