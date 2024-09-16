<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RentBookRequest;
use App\Http\Requests\ReturnBookRequest;

class RentalController extends Controller
{
    public function rentBook(RentBookRequest $request, Book $book) 
    {
        $rental = new Rental();
        $rental->user_id = auth()->id();
        $rental->book_id = $book->id;
        $rental->rented_on = now();
        $rental->due_on = now()->addWeeks(2);
        $rental->save();
    
        $book->decrement('available_copies');
    
        return response()->json(['status' => true, 'message' => 'Book rented successfully']);
    }

    public function returnBook(ReturnBookRequest $request, Book $book)
    {
        $rental = Rental::where('user_id', auth()->id())
                        ->where('book_id', $book->id)
                        ->whereNull('returned_on')
                        ->first();

        $rental->returned_on = now();
        $rental->overdue = $rental->due_on < now();
        $rental->save();

        $book->increment('available_copies');

        return response()->json(['status' => true, 'message' => 'Book returned successfully']);
    }

}
