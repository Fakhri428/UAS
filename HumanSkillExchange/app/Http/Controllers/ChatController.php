<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\ExchangeRequest;
use App\Models\ExchangeProgress;
use App\Notifications\ExchangeAcceptedNotification;
use App\Notifications\ExchangeCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $conversations = $user->conversations()
            ->with(['user1', 'user2', 'latestMessage'])
            ->get();
        return view('chat.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            abort(403);
        }

        $conversation->messages()->where('sender_id', '!=', $user->id)->update(['is_read' => true]);

        $conversations = $user->conversations()
            ->with(['user1', 'user2', 'latestMessage'])
            ->get();

        $messages = $conversation->messages()->with('sender')->oldest()->get();

        $exchangeRequest = $conversation->exchangeRequest;

        return view('chat.show', compact('conversation', 'conversations', 'messages', 'exchangeRequest'));
    }

    public function createWithUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('chat.index')->with('error', 'Tidak bisa chat dengan diri sendiri!');
        }

        $authUser = Auth::user();
        $conversation = Conversation::where(function ($q) use ($authUser, $user) {
            $q->where('user1_id', $authUser->id)->where('user2_id', $user->id);
        })->orWhere(function ($q) use ($authUser, $user) {
            $q->where('user1_id', $user->id)->where('user2_id', $authUser->id);
        })->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user1_id' => min($authUser->id, $user->id),
                'user2_id' => max($authUser->id, $user->id),
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'type' => 'text',
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Pesan terkirim!');
    }

    public function storeProgress(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        
        // Validate conversation has exchange request
        abort_if(! $conversation->exchangeRequest, 403);
        
        $exchangeRequest = $conversation->exchangeRequest;
        
        abort_unless(
            (int) $exchangeRequest->from_user_id === (int) $user->id || (int) $exchangeRequest->to_user_id === (int) $user->id, 
            403
        );
        abort_unless(in_array($exchangeRequest->status, ['accepted', 'in_progress'], true), 422);

        $data = $request->validate([
            'progress_note' => ['required', 'string', 'max:2000'],
            'file_url' => ['nullable', 'url', 'max:255'],
        ]);

        // Create ExchangeProgress
        $progress = ExchangeProgress::create([
            'exchange_request_id' => $exchangeRequest->id,
            'user_id' => $user->id,
            'progress_note' => $data['progress_note'],
            'file_url' => $data['file_url'] ?? null,
        ]);

        // Create Message with type progress
        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'content' => '📊 Update Progress: ' . $data['progress_note'],
            'type' => 'progress',
            'metadata' => [
                'progress_id' => $progress->id,
                'progress_note' => $data['progress_note'],
                'file_url' => $data['file_url'] ?? null,
            ],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Progress berhasil dikirim!');
    }

    public function exchangeAction(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        
        // Validate conversation has exchange request
        abort_if(! $conversation->exchangeRequest, 403);
        
        $exchangeRequest = $conversation->exchangeRequest;
        
        abort_unless(
            (int) $exchangeRequest->from_user_id === (int) $user->id || (int) $exchangeRequest->to_user_id === (int) $user->id, 
            403
        );

        $data = $request->validate([
            'action' => ['required', 'in:start,complete'],
        ]);

        $action = $data['action'];

        if ($action === 'start') {
            abort_unless(in_array($exchangeRequest->status, ['accepted', 'in_progress'], true), 422);
            $exchangeRequest->update(['status' => 'in_progress']);
            
            // Create system message
            $conversation->messages()->create([
                'sender_id' => $user->id,
                'content' => '🚀 Exchange dimulai!',
                'type' => 'system',
            ]);
        }

        if ($action === 'complete') {
            abort_unless(in_array($exchangeRequest->status, ['accepted', 'in_progress', 'completed'], true), 422);

            $exchangeRequest->update([
                'completed_by_from_user' => (int) $exchangeRequest->from_user_id === (int) $user->id || $exchangeRequest->completed_by_from_user,
                'completed_by_to_user' => (int) $exchangeRequest->to_user_id === (int) $user->id || $exchangeRequest->completed_by_to_user,
            ]);

            $freshExchange = $exchangeRequest->fresh();
            if ($freshExchange->completed_by_from_user && $freshExchange->completed_by_to_user) {
                $exchangeRequest->update(['status' => 'completed']);
                // Send notifications
                $exchangeRequest->fromUser->notify(new ExchangeCompletedNotification($exchangeRequest));
                $exchangeRequest->toUser->notify(new ExchangeCompletedNotification($exchangeRequest));
                
                // Create system message
                $conversation->messages()->create([
                    'sender_id' => $user->id,
                    'content' => '✅ Exchange selesai! Silakan beri review.',
                    'type' => 'system',
                ]);
            } else {
                $exchangeRequest->update(['status' => 'in_progress']);
                
                // Create system message
                $conversation->messages()->create([
                    'sender_id' => $user->id,
                    'content' => '⏳ Menunggu konfirmasi selesai dari partner.',
                    'type' => 'system',
                ]);
            }
        }

        $conversation->update(['last_message_at' => now()]);

        return back()->with('status', 'Status exchange berhasil diperbarui!');
    }
}
