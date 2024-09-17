<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use Illuminate\Support\Facades\Log;

class MarkOverdueRentals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-overdue-rentals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marking Overdue Rentals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overdueRentals = Rental::whereNull('returned_on')
                            ->where('due_on', '<', now())
                            ->get();

        foreach ($overdueRentals as $rental) {
            $rental->overdue = true;
            $rental->save();

            $mailData = [
                'title' => $rental->book->title,
                'overdueDate' => now()
            ];
            // Send email notification
            $email = new OverdueRentalMail($mailData);
            Mail::to($rental->user->email)->send($email);
            Log::info("Overdue Mail Sent to ".$rental->user->email);
            // Sending Notification
            auth()->notify(new OverdueRentalNotification($rental));
            Log::info('Overdue Notification sent to user '. auth()->name());
        }
    }
}
