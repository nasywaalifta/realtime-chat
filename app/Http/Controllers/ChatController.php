<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        Auth::user()->update([
            'last_seen' => now(),
        ]);

        $userId = $request->get('user');
        $groupId = $request->get('group');

        $receiver = null;
        $conversation = null;
        $chatTitle = 'Pilih chat terlebih dahulu';

        if ($userId) {
            $receiver = User::findOrFail($userId);

            $conversation = Conversation::where('type', 'private')
                ->whereHas('users', function ($query) {
                    $query->where('users.id', Auth::id());
                })
                ->whereHas('users', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'type' => 'private',
                    'name' => 'Private Chat',
                ]);

                $conversation->users()->attach([
                    Auth::id(),
                    $userId,
                ]);
            }

            $chatTitle = 'Chat dengan ' . $receiver->name;
        }

        if ($groupId) {
            $conversation = Conversation::where('type', 'group')
                ->where('id', $groupId)
                ->firstOrFail();

            if (!$conversation->users()->where('user_id', Auth::id())->exists()) {
                $conversation->users()->attach(Auth::id());
            }

            $chatTitle = 'Group: ' . $conversation->name;
        }

        if ($conversation) {
            $messages = Message::with('user')
                ->where('conversation_id', $conversation->id)
                ->latest()
                ->take(30)
                ->get()
                ->reverse();
        } else {
            $messages = collect();
        }

        $users = User::where('id', '!=', Auth::id())->get();
        $groups = Conversation::where('type', 'group')->get();

        return view('chat.index', compact(
            'messages',
            'users',
            'groups',
            'conversation',
            'chatTitle'
        ));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'conversation_id' => 'required',
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        Auth::user()->update([
            'last_seen' => now(),
        ]);

        broadcast(new MessageSent($message))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('user'),
            ]);
        }

        if ($request->user_id) {
            return redirect()->route('chat.index', [
                'user' => $request->user_id,
            ]);
        }

        if ($request->group_id) {
            return redirect()->route('chat.index', [
                'group' => $request->group_id,
            ]);
        }

        return redirect()->route('chat.index');
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $group = Conversation::create([
            'type' => 'group',
            'name' => $request->name,
        ]);

        $group->users()->attach(Auth::id());

        return redirect()->route('chat.index', [
            'group' => $group->id,
        ]);
    }
}