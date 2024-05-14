<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::group(['roles' => 'roles', 'as' => 'roles.'], function () {
        Route::get('/list-role', [RoleController::class, 'list'])->name('list');
        Route::get('/detail-role', [RoleController::class, 'getDetailRole'])->name('getDetailRole');
    });

    Route::resource('users', UserController::class);
    Route::group(['users' => 'users', 'as' => 'users.'], function () {
        Route::get('/list-users', [UserController::class, 'list'])->name('list');
        Route::post('/getRoles', [UserController::class, 'getRole'])->name('getRole');
        Route::get('/getUser/{id}', [UserController::class, 'getUser'])->name('getUser');
    });

    Route::resource('articles', ArticleController::class);
    Route::resource('categories', CategoryController::class);
});
