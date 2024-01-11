<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ChatEvent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ChatController extends Controller
{
    public function sendMessageToAdmin(Request $request)
    {
        $token = $request->input('guest_token');
        $content = $request->input('content');
            $recipient = User::where('role', 'customer_care')->first();

            if (!$recipient) {
                return response()->json(['error' => 'Không tìm thấy người dùng có vai trò customer_care'], 404);
            }

            $message = new Message();
            $message->content = $content;
            $message->sender_id = $token;
            $message->recipient_id = $recipient->id;
            $message->sender_type = 'customer';
            
            $message->save();
            broadcast(new ChatEvent($message,$token));
            return response()->json(['success' => true], 200);
    }
    public function replyMessageToGuest(Request $request)
    {
        if (Auth::check()) {
            $token = $request->input('guest_token');
            $content = $request->input('content');
            $recipient = User::where('role', 'customer_care')->first();

            if (!$recipient) {
                return response()->json(['error' => 'Không tìm thấy người dùng có vai trò customer_care'], 404);
            }
            $message = new Message();
            $message->content = $content;
            $message->sender_id = $recipient->id;
            $message->recipient_id = $token;
            $message->sender_type = 'admin';

            $message->save();
            event($e = new ChatEvent($message,$token));

                return response()->json(['success' => true], 200);
            
        } else {
            return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
        }
    }

    public function guestGetMessage($token)
    {
        $messages = Message::where(function ($query) use ($token) {
            $query->where('sender_id', $token)
                ->orWhere('recipient_id', $token);
        })
            ->get();
        if(Auth::check()){
            Message::where('sender_id', $token)
        ->orWhere('recipient_id', $token)
        ->where('read', 0) // Chưa đọc
        ->update(['read' => 1]); // Đánh dấu là đã đọc
        }
        return response()->json(['data' => $messages], 200);
    }

    public function adminGetMessage()
{
    try {
        // Lấy danh sách tin nhắn của customer, sắp xếp theo thời gian mới nhất
        $customerMessages = Message::where('sender_type', 'customer')
            ->orderByDesc('created_at')
            ->get();

        // Lấy danh sách token của customer
        $customerTokens = $customerMessages->pluck('sender_id')->unique()->values()->map(function ($token) {
            return (string) $token;
        })->all();

        // Đếm số tin nhắn chưa được đọc của mỗi customer
        $unreadMessagesCount = [];
        foreach ($customerTokens as $token) {
            $unreadCount = Message::where('sender_id', $token)
                ->where('sender_type', 'customer')
                ->where('read', 0)
                ->count();

            $unreadMessagesCount[$token] = $unreadCount;
        }

        return response()->json([
            'customer_tokens' => $customerTokens,
            'customer_messages' => $customerMessages,
            'unread_messages_count' => $unreadMessagesCount
        ], 200);
    } catch (\Exception $exception) {
        return response()->json(['error' => 'Đã xảy ra lỗi khi lấy tin nhắn và danh sách token'], 500);
    }
}

}
