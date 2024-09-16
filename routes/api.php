<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\BookController;

Route::get('/books', [BookController::class, 'searchBooks']);
Route::post('/books/{book}/rent', [RentalController::class, 'rentBook']);
Route::post('/books/{book}/return', [RentalController::class, 'returnBook']);
Route::post('/return', [RentalController::class, 'returnBook']);
Route::get('/rental-history', [RentalController::class, 'rentalHistory']);
Route::get('/stats', [RentalController::class, 'stats']);