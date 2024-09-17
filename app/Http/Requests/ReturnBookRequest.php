<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rental;

class ReturnBookRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id', // Example: to check for logged-in users
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Get the injected Book model from the route (Route Model Binding)
            $book = $this->route('book'); 

            // Find the active rental for the user and this book
            $rental = Rental::where('book_id', $book->id)
                            ->where('user_id', auth()->id())
                            ->whereNull('returned_on')
                            ->first();

            // Check if there is an active rental for this book by the user
            if (!$rental) {
                $validator->errors()->add('book_id', 'You do not have an active rental for this book.');
            }

            if (!empty($validator->errors())) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }
        });
    }

    public function messages()
    {
        return [
            'user_id.required' => 'You must be logged in to return a book.',
        ];
    }
}
