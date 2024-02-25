<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ChatEvent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatController extends Controller
{
    public function index()
    {

        return view("admin.chat.index");
    }
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
        broadcast(new ChatEvent($message, $token));
        return response()->json(['success' => true], 200);
    }
    public function replyMessageToGuest(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
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
            $message->read = 1;
            $message->save();
            broadcast(new ChatEvent($message, $token));

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
        if (JWTAuth::check()) {
            Message::where('sender_id', $token)
                ->orWhere('recipient_id', $token)
                ->where('read', 0)
                ->update(['read' => 1]); 
        }
        return response()->json(['data' => $messages], 200);
    }

    public function adminGetMessage()
    {
        try {
            // Lấy danh sách token của customer
            $customerTokens = Message::where('sender_type', 'customer')
                ->orderByDesc('created_at')
                ->pluck('sender_id')
                ->unique()
                ->values()
                ->map(function ($token) {
                    return (string) $token;
                })
                ->all();

            // Lấy tin nhắn mới nhất và số tin nhắn chưa đọc của mỗi customer
            $customerData = [];
            foreach ($customerTokens as $token) {
                $latestMessage = Message::where('sender_id', $token)
                    ->where('sender_type', 'customer')
                    ->orderByDesc('created_at')
                    ->first();

                $unreadCount = Message::where('sender_id', $token)
                    ->where('sender_type', 'customer')
                    ->where('read', 0)
                    ->count();

                $customerData[] = [
                    'customer_token' => $token,
                    'latest_message' => $latestMessage,
                    'unread_messages_count' => $unreadCount
                ];
            }

            return response()->json([
                'customer_data' => $customerData
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Đã xảy ra lỗi khi lấy tin nhắn và danh sách token'], 500);
        }
    }
}
