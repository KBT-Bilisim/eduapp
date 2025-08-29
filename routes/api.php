<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TodoApiController;

// Todo API Routes
Route::middleware('auth')->group(function () {
    Route::apiResource('todos', TodoApiController::class);
    Route::patch('todos/{id}/status', [TodoApiController::class, 'updateStatus'])->name('todos.update-status');
});
