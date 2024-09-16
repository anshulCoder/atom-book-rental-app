<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Book;
use App\Models\User;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'rented_on',
        'due_on',
        'returned_on',
        'overdue',
        'user_id',
        'book_id'
    ];

    protected $dates = ['rented_on', 'due_on', 'returned_on'];

    protected $casts = [
        'overdue' => 'boolean'
    ];

    /**
     * Get the user that owns the Rental
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that owns the Rental
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
