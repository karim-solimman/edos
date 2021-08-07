<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InvController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
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

Route::get('/users', [UserController::class, 'index']);
Route::post('/registration', [RegistrationController::class, 'registration']);
Route::post('/users/checkemail', [RegistrationController::class, 'check_email']);
Route::post('/users/passwordset', [RegistrationController::class, 'set_password']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/check_email/{email}', [UserController::class, 'checkEmail']);


Route::middleware('auth:sanctum')->group(function() {
    Route::get('/dashboard',[UserController::class, 'dashboard']);

    Route::post('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'userProfile']);
    Route::post('/users/create', [RegistrationController::class, 'create']);
    Route::get('/users/resetpassword/{id}', [UserController::class, 'resetPassword']);
    Route::post('/users/addinv', [UserController::class, 'addInv']);
    Route::post('/users/removeinv', [UserController::class, 'removeInv']);
    Route::get('/users/deletealluserinvs/{id}', [UserController::class, 'removeAllUserInvs']);

    Route::get('/invs',[InvController::class,'index']);
    Route::get('/invforusers', [InvController::class, 'index_groupBy']);
    Route::post('/invs/adduser', [InvController::class, 'addUser']);
    Route::post('/invs/removeuserbydate', [InvController::class, 'removeUserByDate']);
    Route::post('/invs/removeuser', [InvController::class, 'removeUser']);
    Route::get('/invs/{id}',[InvController::class, 'profile']);
    Route::post('/invs/create',[InvController::class, 'create']);
    Route::post('/invs/delete', [InvController::class, 'removeInv']);

    Route::get('/roles',[RoleController::class, 'index']);
    Route::post('/roles/add',[UserController::class, 'attachRole']);
    Route::post('/roles/remove',[UserController::class, 'detachRole']);

    Route::get('/departments',[DepartmentController::class, 'index']);
    Route::get('/departments/{id}', [DepartmentController::class, 'profile']);
    Route::post('/departments/create', [DepartmentController::class, 'create']);

    Route::get('/rooms',[RoomController::class, 'index']);
    Route::get('/rooms/{id}',[RoomController::class, 'profile']);
    Route::post('/rooms/create',[RoomController::class,'create']);

    Route::get('/courses',[CourseController::class,'index']);
    Route::post('/courses/create',[CourseController::class, 'create']);
    Route::get('/courses/{id}',[CourseController::class, 'profile']);
    Route::get('/courses/{id}/remove',[CourseController::class,'delete']);
});
