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
}
