<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatOnlyResource;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chat = Chat::with(["Host", "ChatUser", "messages"])->where("user1", auth()->user()->id)->orWhere("user2", auth()->user()->id)->get();
        return okResponse("chat fetched", ChatOnlyResource::collection($chat));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $chatid = null;
        $message = null;
    
        $fixedCandidateId = 1; // 👈 your test candidate user_id
    
        if ($request->chat_id) {
            $message = Message::create([
                "message" => $request->message,
                "user_id" => auth()->user()->id,
                "chat_id" => $request->chat_id
            ]);
            $chatid = $request->chat_id;
        } else {
            $chat = Chat::where(function($q) use ($fixedCandidateId) {
                $q->where("user1", auth()->user()->id)
                  ->where("user2", $fixedCandidateId);
            })->orWhere(function($q) use ($fixedCandidateId) {
                $q->where("user1", $fixedCandidateId)
                  ->where("user2", auth()->user()->id);
            })->first();
    
            if (!$chat) {
                $chat = Chat::create([
                    "user1" => auth()->user()->id,
                    "user2" => $fixedCandidateId,
                ]);
            }
    
            $chatid = $chat->id;
    
            $message = Message::create([
                "message" => $request->message,
                "user_id" => auth()->user()->id,
                "chat_id" => $chatid
            ]);
        }
    
        broadcast(new \App\Events\MessageSent($message))->toOthers();
    
        $chat = Chat::with(["Host", "ChatUser", "messages"])
            ->where("id", $chatid)
            ->first();
    
        return okResponse("chat sent", new ChatResource($chat));
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $chat = Chat::with(["Host", "ChatUser", "messages"])->where("id", $id)->first();
        if(!$chat) return errorResponse("Chat not found", [], 404);
        if($chat->user1 != auth()->user()->id && $chat->user2 != auth()->user()->id) return errorResponse("Chat not found", [], 404);
        return okResponse("chat fetched", new ChatResource($chat));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
