<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReturnBookRequest;
use App\Models\Rental;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class RentalController extends Controller
{
    public function rentBook(Request $request, Book $book) 
    {
        if ($book->available_copies < 1) {
            return response()->json([
                'status' => false,
                'error' => 'Book is not available for rent.',
            ], 422);
        }
        
        // Check if the user already has an unreturned rental for the same book
        $existingRental = Rental::where('book_id', $book->id)
                                ->where('user_id', auth()->id())
                                ->whereNull('returned_on')
                                ->first();
                                
        if ($existingRental) {
            return response()->json([
                'status' => false,
                'error' => 'You already have this book rented.',
            ], 422);
        }

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

    public function rentalHistory()
    {
        $rentals = Rental::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();

        return response()->json(['status' => true, 'rentals' => $rentals]);
    }

    public function rentalStats()
    {
        $mostPopularBook = Book::withCount('rentals')->orderBy('rentals_count', 'desc')->first();
        $leastPopularBook = Book::withCount('rentals')->orderBy('rentals_count', 'asc')->first();
        $mostOverdueBook = Book::whereHas('rentals', function ($query) {
            $query->where('overdue', true);
        })->withCount('rentals')->orderBy('rentals_count', 'desc')->first();

        return response()->json([
            'status' => true,
            'most_popular' => $mostPopularBook,
            'least_popular' => $leastPopularBook,
            'most_overdue' => $mostOverdueBook,
        ]);
    }

    private function validateRentBook($book)
    {
        
    }

}
