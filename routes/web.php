<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\GroupController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [StudentController::class, 'index'])->name('welcome');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('manager')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('manager');
    Route::get('/groups', [GroupController::class, 'index'])->name('manager.groups');
    Route::get('/schools', [SchoolController::class, 'index'])->name('manager.schools');
});
