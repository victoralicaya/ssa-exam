<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/add', [UserController::class, 'create'])->name('users.create');
    Route::get('users/{user}/show', [UserController::class, 'show'])->name('users.show');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('users/{user}/update', [UserController::class, 'update'])->name('users.update');

    Route::delete('users/{userId}/permanentlyDeleted', [UserController::class, 'permanentlyDelete'])->name('users.delete');

    Route::softDeletes('users', UserController::class);
});
