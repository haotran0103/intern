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


    Route::get('post', [PostController::class, 'index']);

    Route::get('/posts/filter/{categoryId}', [PostController::class, 'filterByCategory']);

    Route::get('category', [CategoryController::class, 'index']);
    Route::get('all-categories', [CategoryController::class, 'getIntroductionChild']);

    Route::get('get-answer', [ChatBotController::class, 'index']);
    Route::post('get-answer', [ChatBotController::class, 'getAnswer']);

    Route::get('/getParentCategory', [CategoryController::class, 'getParentCategory']);
    Route::get('/getSubCategory/{id}', [CategoryController::class, 'getSubCategory']);
    Route::post('/categories/{id}', [CategoryController::class, 'updatecategory']);

    Route::post('/send-message-to-admin', [ChatController::class, 'sendMessageToAdmin']);
    Route::post('/reply-message-to-guest', [ChatController::class, 'replyMessageToGuest']);
    Route::get('/guest-get-message/{token}', [ChatController::class, 'guestGetMessage']);

    Route::post('add-answer', [ChatBotController::class, 'addQuestion']);
    Route::post('edit-answer', [ChatBotController::class, 'editData']);
    Route::delete('delete-answer', [ChatBotController::class, 'deleteData']);

    Route::get('/ReadSetting', [SettingController::class, 'ReadSetting']);
    Route::middleware(['auth'])->group(function () {

        Route::get('user/{user}', [UserController::class, 'show']);
        Route::put('user/{user}', [UserController::class, 'update']);

        Route::post('category', [CategoryController::class, 'store']);
        Route::get('category/{category}', [CategoryController::class, 'show']);
        Route::put('category/{category}', [CategoryController::class, 'update']);
        Route::delete('category/{category}', [CategoryController::class, 'destroy']);

        Route::post('post', [PostController::class, 'store']);
        Route::get('post/{post}', [PostController::class, 'show']);
        // Route::put('post/{post}', [PostController::class, 'update']);
        Route::delete('post/{post}', [PostController::class, 'destroy']);

        Route::get('/admin-get-message', [ChatController::class, 'adminGetMessage']);

        Route::post('/postStatus', [UpdateStatusController::class, 'postStatus']);

        Route::get('/postByCategory/{id}', [PostController::class, 'getAllbyCategory']);
        Route::post('/uploadPostFile', [PostController::class, 'uploadPostFile']);
        Route::post('/updatePost', [PostController::class, 'update']);

        Route::post('/permanentlyDeleteUser', [UserController::class, 'permanentlyDeleteUser']);

        Route::post('/upload-images', [ImageController::class, 'uploadImagePost']);
        Route::post('/banner-images', [ImageController::class, 'uploadImageBanner']);
        Route::get('/clearTempImages', [ImageController::class, 'clearTempImages']);
        Route::post('/remove-image', [ImageController::class, 'removeImage']);

        
        Route::post('/AddSetting', [SettingController::class, 'AddSetting']);
        Route::post('/UpdateSetting/{id}', [SettingController::class, 'UpdateSetting']);
        Route::middleware(['auth.root'])->group(function () {
            // Route::get('/post_history', [HistoryController::class, 'post_history']);
            // Route::get('/user_history', [HistoryController::class, 'user_history']);
            Route::post('/userStatus', [UpdateStatusController::class, 'userStatus']);
            Route::get('user', [UserController::class, 'index']);
            Route::delete('user/{user}', [UserController::class, 'destroy']);
            Route::get('/trashed', [TrashController::class, 'getTrashedUser']);
            Route::put('/restore/{id}', [TrashController::class, 'restoreTrashedUser']);
            Route::get('/trashed-posts', [TrashController::class, 'getTrashedPosts']);
            Route::put('/restore-posts/{id}', [TrashController::class, 'restoreTrashedPost']);
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

        Route::post('/upload-file-post', function (Request $request) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $filePath = $file->store('uploads', 'public');

                return response()->json(['file_path' => $filePath], 200);
            }

            return response()->json(['message' => 'No file uploaded'], 400);
        });
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


    Route::group([

        'middleware' => 'api',
        'prefix' => 'auth'

    ], function ($router) {

        Route::post('login', [AuthController::class, 'login']);

        Route::post('register', [AuthController::class, 'register'])->middleware('auth.root');

        Route::post('logout', [AuthController::class, 'logout']);

        Route::post('refresh', [AuthController::class, 'refresh']);

        Route::post('updatePassword', [AuthController::class, 'updatePassword']);

        Route::get('profile', [AuthController::class, 'profile']);
    });
});
