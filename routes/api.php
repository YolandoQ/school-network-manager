<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SchoolController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('students')->group(function () {
    Route::post('/create', [StudentController::class, 'create']);
    Route::put('/{id}', [StudentController::class, 'update']);
    Route::delete('/{id}', [StudentController::class, 'destroy']);
});

Route::prefix('groups')->group(function () {
    Route::post('/create', [GroupController::class, 'create']);
    Route::put('/{id}', [GroupController::class, 'update']);
    Route::delete('/{id}', [GroupController::class, 'destroy']);
});

Route::prefix('schools')->group(function () {
    Route::post('/create', [SchoolController::class, 'create']);
    Route::put('/{id}', [SchoolController::class, 'update']);
    Route::delete('/{id}', [SchoolController::class, 'destroy']);
});
