<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\User;
use App\Notifications\ContactBirthdayNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetContactBirthdayList extends Command
{
    protected $signature = 'app:get-contact-birthday-list';

    protected $description = "get today's list of customer birthdays";

    public function handle()
    {
        $contact_birthdays = Contact::with('contactable')
            ->where('birthdate_month', (today()->month - 1))
            ->where('birthdate_day', (today()->day))
            ->get();

        // usuarios a notificar
        $users = User::whereIn('id', [1, 2, 3])->get();

        foreach ($contact_birthdays as $contact) {
            foreach ($users as $user) {
                $user->notify(new ContactBirthdayNotification($contact));
            }
        }

        Log::info('app:get-contact-birthday-list executed successfully. There where ' . $contact_birthdays->count() . ' birthday(s)');
    }
}
