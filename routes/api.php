<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function() {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');

    Route::prefix('util')->controller('UtilController')->group(function(){
        Route::post('send/mail', 'sendEmail');
    });
});

Route::middleware('auth:api')->group(function() {
    Route::prefix('products')->controller('ProductController')->group(function(){
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('latest', 'latest')->withoutMiddleware('auth:api');
        Route::get('active', 'active')->withoutMiddleware('auth:api');
        Route::post('datatables', 'datatables');
        Route::get('{id}', 'find');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'delete');
    });

    Route::prefix('users')->controller('UserController')->group(function(){
        Route::get('', 'index');
        Route::post('', 'store');
        Route::post('datatables', 'datatables');
        Route::get('{id}', 'find');
        Route::put('{id}', 'update');
        Route::put('{id}/approve', 'approve');
        Route::delete('{id}', 'delete');
    });

    Route::prefix('files')->controller('FileController')->group(function(){
        Route::get('', 'download');
        Route::get('images', 'images')->withoutMiddleware('auth:api');
        Route::post('', 'upload');
    });
    Route::prefix('dashboard')->controller('DashboardController')->group(function(){
        Route::get('card', 'card');
        // Route::post('datatables', 'datatables');

    });
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
