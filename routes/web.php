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

    Route::group(['prefix' => 'roles', 'roles' => 'roles', 'as' => 'roles.'], function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');

        Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        Route::get('/{id}/details', [RoleController::class, 'show'])->name('show');

        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');

        Route::get('/list', [RoleController::class, 'list'])->name('list');
        Route::get('/detail', [RoleController::class, 'getDetailRole'])->name('getDetailRole');
    });


    Route::group(['prefix' => 'users', 'users' => 'users', 'as' => 'users.'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');

        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::get('/{id}/details', [UserController::class, 'show'])->name('show');

        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');

        Route::get('/list', [UserController::class, 'list'])->name('list');
        Route::post('/getRoles', [UserController::class, 'getRole'])->name('getRole');
        Route::get('/getUser/{id}', [UserController::class, 'getUser'])->name('getUser');
    });

    // Route::resource('articles', ArticleController::class);
    Route::group(['prefix' => 'articles', 'articles' => 'articles', 'as' => 'articles.'], function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [ArticleController::class, 'edit'])->name('edit');

        Route::post('/', [ArticleController::class, 'store'])->name('store');

        Route::get('/list', [ArticleController::class, 'list'])->name('list');
        Route::post('/upload-img', [ArticleController::class, 'upload'])->name('upload');

        Route::get('/create', [ArticleController::class, 'create'])->name('create');

        Route::get('/get-category', [ArticleController::class, 'getCategory'])->name('get-category');
        Route::patch('/update-status/{id}', [ArticleController::class, 'updateStatus'])->name('update-status');
        Route::put('/{id}/edit', [ArticleController::class, 'update'])->name('update');

        Route::delete('/{id}', [ArticleController::class, 'destroy'])->name('destroy');
    });

    // Route::resource('categories', CategoryController::class)->except(['show']);
    Route::group(['prefix' => 'categories', 'categories' => 'categories', 'as' => 'categories.'], function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');

        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');

        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');

        Route::get('/list', [CategoryController::class, 'list'])->name('list');
        Route::get('/getCategory/{id}', [CategoryController::class, 'getCategory'])->name('getCategory');
    });
});
