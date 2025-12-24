<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class SendWelcomeEmail
{
    public function handle(Verified $event)
    {
        Mail::to($event->user->email)->queue(
            new WelcomeMail($event->user)
        );
    }
}
