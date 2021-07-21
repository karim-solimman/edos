<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
Route::post('/registration', [\App\Http\Controllers\Auth\RegistrationController::class, 'registration']);
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('/check_email/{email}', [\App\Http\Controllers\UserController::class, 'checkEmail']);
Route::get('/invs',[\App\Http\Controllers\InvController::class,'index']);


Route::middleware('auth:sanctum')->group(function() {
    Route::get('/dashboard',[\App\Http\Controllers\UserController::class, 'dashboard']);
    Route::post('/profile', [\App\Http\Controllers\UserController::class, 'profile']);
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
    Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'userProfile']);
    Route::post('/invs/removeuser', [\App\Http\Controllers\InvController::class, 'removeUser']);
    Route::get('/invs/{id}',[\App\Http\Controllers\InvController::class, 'profile']);
    Route::post('/invs/add', [\App\Http\Controllers\UserController::class, 'addInv']);
    Route::post('/invs/remove', [\App\Http\Controllers\UserController::class, 'removeInv']);
    Route::post('/roles/add',[\App\Http\Controllers\UserController::class, 'attachRole']);
    Route::post('/roles/remove',[\App\Http\Controllers\UserController::class, 'detachRole']);
    Route::get('/roles',[\App\Http\Controllers\RoleController::class, 'index']);
    Route::get('/departments',[\App\Http\Controllers\DepartmentController::class, 'index']);
    Route::get('/departments/{id}', [\App\Http\Controllers\DepartmentController::class, 'profile']);
    Route::post('/departments/create', [\App\Http\Controllers\DepartmentController::class, 'create']);
    Route::get('/rooms',[\App\Http\Controllers\RoomController::class, 'index']);
    Route::get('/rooms/{id}',[\App\Http\Controllers\RoomController::class, 'profile']);
    Route::post('/rooms/create',[\App\Http\Controllers\RoomController::class,'create']);
    Route::get('/courses',[\App\Http\Controllers\CourseController::class,'index']);
    Route::post('/courses/create',[\App\Http\Controllers\CourseController::class, 'create']);
    Route::get('/courses/{id}',[\App\Http\Controllers\CourseController::class, 'profile']);
    Route::get('/courses/{id}/remove',[\App\Http\Controllers\CourseController::class,'delete']);
});
