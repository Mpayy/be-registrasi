<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function(){
    return response()->json(['message'=>'API has Run !!', 'version' => '1.0']);
});

// route untuk test postman bisa atau tidak dan mengecek validasi bekerja atau tidak (http://127.0.0.1:8000/api/register)
Route::post('register', [\App\Http\Controllers\AuthController::class, 'registration']);

// route login
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);

// route middleware
Route::middleware('auth:sanctum')->group(function(){
    // route user
    Route::apiResource('users', UserController::class);

});
