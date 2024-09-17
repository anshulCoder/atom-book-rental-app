<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function searchBooks(Request $request)
    {
        $title = $request->query('title');
        $genre = $request->query('genre');

        $books = Book::orderBy('created_at', 'desc');

        if(!empty($title)) {
            $books->where('title', 'like', "%{$title}%");
        }

        if (!empty($genre)) {
            $books->where('genre', 'like', "%{$genre}%");
        }

        return response()->json(['status' => true, 'books' => $books->get()]);
    }

    public function bookStats()
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
}
