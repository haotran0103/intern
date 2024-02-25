<?php

use App\Http\Controllers\Api\v1\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\PostController;
use App\Models\setting;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PostController::class, 'postIndex'])->name('post.postIndex');

Route::get('/detail/{id}', [PostController::class, 'detail'])->name('post.detail');

Route::get('/news', [PostController::class, 'allPost'])->name('news');

Route::get('/documents', [PostController::class, 'getAllDocument'])->name('documents');

Route::get('/news-category/{id}', [PostController::class, 'allPostCategory'])->name('news.category');

Route::get('/contact', function () {
    return view('client.contact.index')->with('data', setting::all());
});



//admin routes
Route::get('/login', function () {
    return view('auth.login.index');
});
Route::get('/dashboard', function () {
    return view('admin.dashboard.index');
});
Route::get('/category', function () {
    return view('admin.categories.index');
});
Route::get('/posts', function () {
    return view('admin.post.index');
});
Route::get('/settings', function () {
    return view('admin.setting.index');
});
Route::get('/register', function () {
    return view('auth.register.index');
});
Route::get('/account', function () {
    return view('admin.manage.accounts.index');
});
Route::get('/trash', function () {
    return view('admin.manage.trash.index');
});
Route::get('/chat', [ChatController::class, 'index'])->name('admin.chat');
