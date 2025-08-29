<?php

use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

// Dashboard Routes (Authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Users Routes
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

    // Todo Routes
    Route::get('/todos', [App\Http\Controllers\TodoController::class, 'index'])->name('todos.index');
    Route::get('/todos-simple', [App\Http\Controllers\TodoController::class, 'simpleIndex'])->name('todos.simple-index');
    Route::get('/todos-datatable', [App\Http\Controllers\TodoController::class, 'datatable'])->name('todos.datatable');
    Route::post('/todos', [App\Http\Controllers\TodoController::class, 'store'])->name('todos.store');
    Route::get('/todos/{id}', [App\Http\Controllers\TodoController::class, 'show'])->name('todos.show');
    Route::put('/todos/{id}', [App\Http\Controllers\TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{id}', [App\Http\Controllers\TodoController::class, 'destroy'])->name('todos.destroy');
    Route::patch('/todos/{id}/status', [App\Http\Controllers\TodoController::class, 'updateStatus'])->name('todos.update-status');
    Route::get('/todos-statistics', [App\Http\Controllers\TodoController::class, 'statistics'])->name('todos.statistics');

    // Shopping Routes
    Route::prefix('shopping')->name('shopping.')->group(function () {
        // Listeler
        Route::get('/',             [App\Http\Controllers\ShoppingListController::class, 'index'])->name('index');
        Route::post('/',            [App\Http\Controllers\ShoppingListController::class, 'store'])->name('store');
        Route::get('{shopping}',    [App\Http\Controllers\ShoppingListController::class, 'show'])->name('show');
        Route::put('{shopping}',    [App\Http\Controllers\ShoppingListController::class, 'update'])->name('update');
        Route::delete('{shopping}', [App\Http\Controllers\ShoppingListController::class, 'destroy'])->name('destroy');

        // Ürünler
        Route::post('{shopping}/items',     [App\Http\Controllers\ShoppingItemController::class, 'store'])->name('items.store');
        Route::patch('items/{item}/toggle', [App\Http\Controllers\ShoppingItemController::class, 'togglePurchased'])->name('items.toggle');
        Route::delete('items/{item}',       [App\Http\Controllers\ShoppingItemController::class, 'destroy'])->name('items.destroy');
    });
});
