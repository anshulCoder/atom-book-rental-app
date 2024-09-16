<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function rentBook(Book $book) {
        
        if ($book->available_copies < 1) {
            return response()->json(['message' => 'Book not available'], 400);
        }
    
        $rental = new Rental();
        $rental->user_id = auth()->id();
        $rental->book_id = $request->book_id;
        $rental->rented_on = now();
        $rental->due_on = now()->addWeeks(2);
        $rental->save();
    
        $book->available_copies--;
        $book->save();
    
        return response()->json(['message' => 'Book rented successfully']);
    }

}
