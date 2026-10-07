<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Chat\Entities\ChatModel;
use Auth;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $chats = ChatModel::where('room_id', Auth::user()->id_user)->orderBy('created_at')->get();
        return response()->json($chats);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('chat::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $chat = ChatModel::create([
            'user_id' => Auth::user()->id_user,
            'room_id' => Auth::user()->id_user,
            'sender' => 'user', // default user, admin nanti manual
            'message' => $request->message
        ]);

        return response()->json($chat, 201);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('chat::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('chat::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function srrd()
    {
        $pdf = \PDF::loadview("chat::srrd")
            ->setOptions(['defaultFont' => 'Tahoma'])->setPaper('A4', 'portrait');
        return $pdf->stream();
    }

    public function adminChat()
    {
        return view('chat::admin-chat');
    }

    public function users()
    {
        $users = ChatModel::with('user')->select('*')
            ->whereNotNull('room_id')
            ->groupBy('room_id')
            ->get();

        return response()->json($users);
    }

    public function userChats($userId)
    {
        $chats = ChatModel::where('room_id', $userId)->orderBy('created_at')->get();
        return response()->json($chats);
    }

    public function adminReply(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $chat = ChatModel::create([
            'room_id' => $userId,
            'user_id' => Auth::user()->id_user,
            'sender' => 'admin',
            'message' => $request->message
        ]);

        return response()->json($chat, 201);
    }
}
