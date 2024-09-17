<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Rental;

class BookTest extends TestCase
{

    public function test_can_search_books()
    {
        // Create a user and simulate authentication
        $user = User::factory()->create();

        // Simulate books in the database
        $book = Book::factory()->create([
            'title' => 'The Great Gatsby',
            'genre' => 'Classics',
            'author' => 'F. Scott Fitzgerald',
            'isbn' => '9780743273565',
            'available_copies' => 1
        ]);

        // Act as the user
        $this->actingAs($user)->withHeaders(['Accept' => 'application/json']);

        // Make a GET request to search books
        $response = $this->getJson('/api/books?title=The Great Gatsby');

        // Assert the response is successful and contains the book data
        $response->assertStatus(200)
                ->assertJsonFragment(['title' => 'The Great Gatsby']);
    }

    public function test_can_rent_a_book()
    {
        // Create a user and simulate authentication
        $user = User::factory()->create();

        // Simulate a book
        $book = Book::factory()->create();

        // Act as the user
        $this->actingAs($user)->withHeaders(['Accept' => 'application/json']);

        // Post request to rent a book
        $response = $this->postJson("/api/books/{$book->id}/rent");

        // Assert the response status and that the rental was created
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Book rented successfully']);
    }

    public function test_can_return_a_book()
    {
        // Create a user and simulate authentication
        $user = User::factory()->create();

        // Simulate a book and an active rental
        $book = Book::factory()->create();
        $rental = Rental::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'returned_on' => null,
        ]);

        // Act as the user
        $this->actingAs($user)->withHeaders(['Accept' => 'application/json']);

        // Post request to return a book
        $response = $this->postJson("/api/books/{$book->id}/return");

        // Assert the response status and that the book was returned
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Book returned successfully']);
    }

    public function test_user_can_view_books_stats()
    {
        // Create a user and simulate authentication
        $user = User::factory()->create();

        // Simulate book stats (for example by renting some books)
        $this->actingAs($user)->withHeaders(['Accept' => 'application/json']);

        // Make a GET request to retrieve book stats
        $response = $this->getJson('/api/books/stats');

        // Assert the response is successful and contains the correct stats structure
        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'most_overdue', 'most_popular', 'least_popular']);
    }

}
