<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\AuthController;


Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'index']);
    Route::get('/books', [BookController::class, 'searchBooks'])->name('books.search');
    Route::post('/books/{book}/rent', [RentalController::class, 'rentBook'])->name('books.rent');
    Route::post('/books/{book}/return', [RentalController::class, 'returnBook'])->name('books.return');
    Route::get('/books/stats', [BookController::class, 'bookStats'])->name('books.stats');
    Route::get('/rental/history', [RentalController::class, 'rentalHistory'])->name('rental.history');
});
