<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('category', 'CategoryCrudController');
    Route::crud('category_post', 'Category_PostCrudController');
    Route::crud('comment', 'CommentCrudController');
    Route::crud('like', 'LikeCrudController');
    Route::crud('post', 'PostCrudController');
    Route::crud('users', 'UsersCrudController');
}); // this should be the absolute last line of this file