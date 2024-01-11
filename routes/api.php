<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\PostController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\ImageController;
use App\Http\Controllers\Api\v1\UpdateStatusController;
use App\Http\Controllers\Api\v1\SettingController;
use App\Http\Controllers\Api\v1\BannerImagesController;
use App\Http\Controllers\Api\v1\HistoryController;
use App\Http\Controllers\Api\v1\TrashController;
use App\Http\Controllers\Api\v1\ChatController;
use App\Http\Controllers\Api\v1\ChatBotController;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    /**
     * @OA\Info(
     *   title="API Documentation",
     *   version="1.0.0"
     * )
     */
    Route::resource('user', UserController::class);
    Route::resource('post', PostController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('bannerImages', BannerImagesController::class);

    Route::get('get-answer', [ChatBotController::class, 'index']);
    Route::post('get-answer', [ChatBotController::class, 'getAnswer']);
    Route::post('add-answer', [ChatBotController::class, 'addData']);
    Route::post('edit-answer', [ChatBotController::class, 'editData']);
    Route::delete('delete-answer', [ChatBotController::class, 'deleteData']);


    Route::post('/send-message-to-admin', [ChatController::class, 'sendMessageToAdmin']);
    Route::post('/reply-message-to-guest', [ChatController::class, 'replyMessageToGuest']);
    Route::get('/guest-get-message/{token}', [ChatController::class, 'guestGetMessage']);
    Route::get('/admin-get-message', [ChatController::class, 'adminGetMessage']);

    Route::post('/userStatus', [UpdateStatusController::class, 'userStatus']);
    Route::post('/postStatus', [UpdateStatusController::class, 'postStatus']);
    Route::post('/bannerStatus', [UpdateStatusController::class, 'bannerStatus']);

    Route::get('/trashed-posts', [TrashController::class, 'getTrashedPosts']);
    Route::put('/restore-posts/{id}', [TrashController::class, 'restoreTrashedPost']);
    Route::get('/trashed', [TrashController::class, 'getTrashedUser']);
    Route::put('/restore/{id}', [TrashController::class, 'restoreTrashedUser']);

    Route::get('/getBanner', [BannerImagesController::class, 'getAll']);

    Route::get('/post_history', [HistoryController::class, 'post_history']);
    Route::get('/user_history', [HistoryController::class, 'user_history']);

    Route::get('/getParentCategory', [CategoryController::class, 'getParentCategory']);
    Route::get('/getSubCategory/{id}', [CategoryController::class, 'getSubCategory']);
    Route::get('/getAllCategoriesWithSubcategories', [CategoryController::class, 'getAllCategoriesWithSubcategories']);
    
    Route::get('/postByCategory/{id}', [PostController::class, 'getAllbyCategory']);
    Route::post('/uploadPostFile', [PostController::class, 'uploadPostFile']);
    Route::post('/updatePost/{id}', [PostController::class, 'update']);

    Route::post('/permanentlyDeleteUser', [UserController::class, 'permanentlyDeleteUser']);

    Route::post('/upload-images', [ImageController::class, 'uploadImagePost']);
    Route::post('/banner-images', [ImageController::class, 'uploadImageBanner']);
    Route::get('/clearTempImages', [ImageController::class, 'clearTempImages']);
    Route::post('/remove-image', [ImageController::class, 'removeImage']);

    Route::get('/ReadSetting', [SettingController::class, 'ReadSetting']);
    Route::post('/UpdateSetting', [SettingController::class, 'UpdateSetting']);


    Route::post('/upload-file-post', function (Request $request) {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $filePath = $file->store('uploads', 'public');

            return response()->json(['file_path' => $filePath], 200);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    });
    Route::get('/increase-views/{postId}', function ($postId) {
        $post = Post::find($postId);

        if ($post) {
            $post->views += 1;
            $post->save();

            return response()->json(['message' => 'Số lượt xem đã được tăng'], 200);
        }

        return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
    });
    Route::post('/change-password/{userId}', function (Request $request, $userId) {
        $user = User::find($userId);

        if ($user) {
            if (Hash::check($request->input('current_password'), $user->password)) {
                if ($request->input('new_password') === $request->input('confirm_password')) {
                    $user->password = Hash::make($request->input('new_password'));
                    $user->save();

                    return response()->json(['message' => 'Password changed successfully'], 200);
                } else {
                    return response()->json(['error' => 'New passwords do not match'], 400);
                }
            } else {
                return response()->json(['error' => 'Current password is incorrect'], 400);
            }
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    });
    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     operationId="register",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         description="User registration details",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA.Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="user", type="object"),
     *             @OA.Property(property="authorization", type="object",
     *                 @OA\Property(property="token", type="string", example="your_jwt_token"),
     *                 @OA.Property(property="type", type="string", example="bearer")
     *             )
     *         )
     *     )
     * )
     */
    
    Route::post('login', [AuthController::class, 'login']);
    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     operationId="register",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         description="User registration details",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="authorization", type="object",
     *                 @OA\Property(property="token", type="string", example="your_jwt_token"),
     *                 @OA\Property(property="type", type="string", example="bearer")
     *             )
     *         )
     *     )
     * )
     */
    Route::post('register', [AuthController::class, 'register']);
    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout the user",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA.Property(property="status", type="string", example="success"),
     *             @OA.Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     )
     * )
     */
    Route::post('logout', [AuthController::class, 'logout']);
    /**
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     summary="Refresh the user's token",
     *     operationId="refresh",
     *     tags={"Authentication"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA.Property(property="status", type="string", example="success"),
     *             @OA.Property(property="user", type="object"),
     *             @OA.Property(property="authorization", type="object",
     *                 @OA.Property(property="token", type="string", example="your_refreshed_jwt_token"),
     *                 @OA.Property(property="type", type="string", example="bearer")
     *             )
     *         )
     *     )
     * )
     */
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::post('updatePassword', [AuthController::class, 'updatePassword']);
});

