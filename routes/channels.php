<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// TEMPORARY: Allow all chat channels to test WebSocket connection
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Just return true to allow all authenticated users (FOR TESTING ONLY!)
    return true;
});