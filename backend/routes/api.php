<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

///////////////////////


//Public routes

    //Authentication module
    Route::group(['prefix'=>'auth'], function() {
        Route::post('/register', [AuthController::class, 'register']); //works
        Route::post('/login', [AuthController::class, 'login']); //works
        Route::post('/password-reset', [AuthController::class, 'send_email']); //works
        Route::post('/password-reset/{confirm_token}', [AuthController::class, 'reset_password']); //works
    });

    //User module
    Route::group(['prefix'=>'users'], function() {
        Route::get('', [UserController::class, 'index']); //works
        Route::get('{user_id}', [UserController::class, 'show']); //works
        
    });

    //Category module
    Route::group(['prefix'=>'categories'], function() {
        Route::get('', [CategoryController::class, 'index']); //works
        Route::get('{category_id}', [CategoryController::class, 'show']); //works
        Route::get('{category_id}/posts', [CategoryController::class, 'show_posts']); //works
    });

    //Post module
    Route::group(['prefix'=>'posts'], function() {
        Route::get('', [PostController::class, 'index']); //works
        Route::get('{post_id}', [PostController::class, 'show']); //works
        Route::get('{post_id}/categories', [PostController::class, 'show_categories']); //works
        Route::get('{post_id}/comments', [PostController::class, 'show_comments']); //works
        Route::get('{post_id}/like', [PostController::class, 'show_likes']); //works
        Route::get('filter', [PostController::class, 'filter']); //works
    });

    //Comment module
    Route::group(['prefix'=>'comments'], function() {
        Route::get('{comment_id}', [CommentController::class, 'show']); //works
        Route::get('{comment_id}/reply', [CommentController::class, 'get_reply']); //works
        Route::get('{comment_id}/like', [CommentController::class, 'show_likes']); //works
    });

///////////////


//Protected routes
Route::group(['middleware'=>['auth:sanctum']], function() {
    Route::post('auth/logout', [AuthController::class, 'logout']); //works

    //User module
    Route::group(['prefix'=>'users'], function() {
        Route::post('', [UserController::class, 'store']); //works
        Route::patch('{user_id}', [UserController::class, 'update']); //works, fucked it
        Route::delete('{user_id}', [UserController::class, 'destroy']); //works
        Route::post('avatar', [UserController::class, 'avatar']); //works
    });

    //Category module
    Route::group(['prefix'=>'categories'], function() {
        Route::post('', [CategoryController::class, 'store']); //works
        Route::patch('{category_id}', [CategoryController::class, 'update']); //works
        Route::delete('{category_id}', [CategoryController::class, 'destroy']); //works
    });
    
    //Post module
    Route::group(['prefix'=>'posts'], function() {
        Route::post('', [PostController::class, 'store']); //works
        Route::patch('{post_id}', [PostController::class, 'update']); //works
        Route::delete('{post_id}', [PostController::class, 'destroy']); //works
        Route::post('{post_id}/comments', [PostController::class, 'create_comment']); //works
        Route::post('{post_id}/like', [PostController::class, 'create_like']); //works
        Route::delete('{post_id}/like', [PostController::class, 'delete_like']); //works
    });

    //Comment module
    Route::group(['prefix'=>'comments'], function() {
        Route::patch('{comment_id}', [CommentController::class, 'update']); //works
        Route::delete('{comment_id}', [CommentController::class, 'destroy']); //works
        Route::post('{comment_id}/reply', [CommentController::class, 'reply_comment']); //works
        Route::post('{comment_id}/like', [CommentController::class, 'create_like']); //works
        Route::delete('{comment_id}/like', [CommentController::class, 'delete_like']); //works
    });
    
});
////////////////



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
