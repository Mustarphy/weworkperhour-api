<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatOnlyResource;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::with(["Host", "ChatUser", "messages"])
            ->where("user1", auth()->user()->id)
            ->orWhere("user2", auth()->user()->id)
            ->get();

        return okResponse("Chats fetched", ChatOnlyResource::collection($chats));
    }

    public function show($id)
    {
        $chat = Chat::with(["Host", "ChatUser", "messages"])
            ->where("id", $id)
            ->first();

        if (!$chat)
            return errorResponse("Chat not found", [], 404);

        if ($chat->user1 != auth()->user()->id && $chat->user2 != auth()->user()->id)
            return errorResponse("Unauthorized", [], 403);

        return okResponse("Chat fetched", new ChatResource($chat));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|integer',
        ]);

        $userId = auth()->user()->id;
        $receiverId = $request->receiver_id;

        // Find or create chat
        $chat = Chat::where(function ($q) use ($userId, $receiverId) {
            $q->where('user1', $userId)->where('user2', $receiverId);
        })->orWhere(function ($q) use ($userId, $receiverId) {
            $q->where('user1', $receiverId)->where('user2', $userId);
        })->first();

        if (!$chat) {
            $chat = Chat::create([
                'user1' => $userId,
                'user2' => $receiverId,
            ]);
        }

        // Create message
        $message = Message::create([
            'message' => $request->message,
            'user_id' => $userId,
            'chat_id' => $chat->id,
        ]);

        // Broadcast new message to Pusher
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        $chat->load(["Host", "ChatUser", "messages"]);

        return okResponse("Message sent", new ChatResource($chat));
    }
}
