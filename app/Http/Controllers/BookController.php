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

        if (empty($title) && empty($genre)) {
            return response()->json(['status' => false, 'error' => 'Please provide title or genre!'], 400);
        }
        $books = Book::where('title', 'ilike', '%' . $title . '%')
                 ->orWhere('genre', 'ilike', '%' . $genre . '%')
                 ->get();

        return response()->json(['status' => true, 'books' => $books]);
    }
}
