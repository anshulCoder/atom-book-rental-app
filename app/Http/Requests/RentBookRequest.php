<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rental;

class RentBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id', // Ensure book exists
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $book = $this->route('book');

            // Check if the book has available copies
            if ($book->available_copies < 1) {
                $validator->errors()->add('book_id', 'Book is not available for rent.');
            }
            
            // Check if the user already has an unreturned rental for the same book
            $existingRental = Rental::where('book_id', $book->id)
                                    ->where('user_id', auth()->id())
                                    ->whereNull('returned_on')
                                    ->first();
                                    
            if ($existingRental) {
                $validator->errors()->add('book_id', 'You already have this book rented.');
            }
        });
    }
}
