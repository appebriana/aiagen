<?php

namespace App\Http\Controllers;

use App\Models\LivechatWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LivechatController extends Controller
{
    public function widgetJs(Request $request)
    {
        $token = $request->get('token');
        $widget = LivechatWidget::where('token', $token)->first();
        if (!$widget || !$widget->is_active) {
            return response('// Livechat widget is inactive or invalid token', 200)
                ->header('Content-Type', 'application/javascript')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        }
        
        // Check domain authorization
        $origin = $request->headers->get('Origin') ?? $request->headers->get('Referer');
        if ($widget->target_domain && $origin) {
            $allowedHost = parse_url($widget->target_domain, PHP_URL_HOST) ?? $widget->target_domain;
            $requestHost = parse_url($origin, PHP_URL_HOST) ?? $origin;
            if (strcasecmp($allowedHost, $requestHost) !== 0 && !str_ends_with(strtolower($requestHost), '.' . strtolower($allowedHost))) {
                return response('// Unauthorized domain', 403)
                    ->header('Content-Type', 'application/javascript');
            }
        }
        
        $department = $widget->department;
        $primaryColor = $widget->primary_color ?? '#4f46e5';
        $aiName = $department->ai_name ?? 'AI Agent';
        $welcomeMessage = $widget->welcome_message ?: "Halo, nama saya {$aiName}. Ada yang bisa saya bantu?";
        $appUrl = url('/');
        
        $response = response()
            ->view('livechat.widget-js', compact('token', 'primaryColor', 'welcomeMessage', 'aiName', 'appUrl', 'department'))
            ->header('Content-Type', 'application/javascript');
            
        $allowOrigin = '*';
        if ($widget->target_domain && $origin) {
            $allowOrigin = rtrim($origin, '/');
        }
        
        return $response->header('Access-Control-Allow-Origin', $allowOrigin)
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    }

    public function initChat(Request $request)
    {
        $token = $request->get('token');
        $widget = LivechatWidget::where('token', $token)->first();
        if (!$widget || !$widget->is_active) {
            return response()->json(['status' => 'error', 'message' => 'Widget inactive'], 404)
                ->header('Access-Control-Allow-Origin', '*');
        }
        
        $origin = $request->headers->get('Origin') ?? $request->headers->get('Referer');
        if ($widget->target_domain && $origin) {
            $allowedHost = parse_url($widget->target_domain, PHP_URL_HOST) ?? $widget->target_domain;
            $requestHost = parse_url($origin, PHP_URL_HOST) ?? $origin;
            if (strcasecmp($allowedHost, $requestHost) !== 0 && !str_ends_with(strtolower($requestHost), '.' . strtolower($allowedHost))) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized domain'], 403);
            }
        }
        
        $department = $widget->department;
        
        $allowOrigin = '*';
        if ($widget->target_domain && $origin) {
            $allowOrigin = rtrim($origin, '/');
        }
        
        $aiName = $department->ai_name ?? 'AI Agent';
        
        return response()->json([
            'status' => 'success',
            'name' => $widget->name,
            'ai_name' => $aiName,
            'primary_color' => $widget->primary_color ?? '#4f46e5',
            'welcome_message' => $widget->welcome_message ?: "Halo, nama saya {$aiName}. Ada yang bisa saya bantu?"
        ])->header('Access-Control-Allow-Origin', $allowOrigin)
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    }

    public function getChats(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'session_id' => 'required'
        ]);
        
        $widget = LivechatWidget::where('token', $request->token)->first();
        if (!$widget) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 404);
        }
        
        $origin = $request->headers->get('Origin') ?? $request->headers->get('Referer');
        if ($widget->target_domain && $origin) {
            $allowedHost = parse_url($widget->target_domain, PHP_URL_HOST) ?? $widget->target_domain;
            $requestHost = parse_url($origin, PHP_URL_HOST) ?? $origin;
            if (strcasecmp($allowedHost, $requestHost) !== 0 && !str_ends_with(strtolower($requestHost), '.' . strtolower($allowedHost))) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized domain'], 403);
            }
        }
        
        $department = $widget->department;
        
        $chats = DB::table('ai_chat_logs')
            ->where('department_id', $department->id)
            ->where('customer_phone', $request->session_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($chat) {
                $chat->formatted_time = \Carbon\Carbon::parse($chat->created_at)->format('H:i');
                return $chat;
            });
            
        $allowOrigin = '*';
        if ($widget->target_domain && $origin) {
            $allowOrigin = rtrim($origin, '/');
        }
            
        return response()->json([
            'status' => 'success',
            'data' => $chats
        ])->header('Access-Control-Allow-Origin', $allowOrigin)
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'session_id' => 'required',
            'message' => 'required|string',
            'name' => 'nullable|string'
        ]);
        
        $widget = LivechatWidget::where('token', $request->token)->first();
        if (!$widget || !$widget->is_active) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or inactive widget'], 404)
                ->header('Access-Control-Allow-Origin', '*');
        }
        
        $origin = $request->headers->get('Origin') ?? $request->headers->get('Referer');
        if ($widget->target_domain && $origin) {
            $allowedHost = parse_url($widget->target_domain, PHP_URL_HOST) ?? $widget->target_domain;
            $requestHost = parse_url($origin, PHP_URL_HOST) ?? $origin;
            if (strcasecmp($allowedHost, $requestHost) !== 0 && !str_ends_with(strtolower($requestHost), '.' . strtolower($allowedHost))) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized domain'], 403);
            }
        }
        
        $department = $widget->department;
        
        $sessionId = $request->session_id;
        $messageText = $request->message;
        $name = $request->name ?? 'Visitor';
        
        // Save visitor message to DB first
        DB::table('ai_chat_logs')->insert([
            'department_id' => $department->id,
            'customer_phone' => $sessionId,
            'channel' => 'livechat',
            'question' => $messageText,
            'answer' => '',
            'model' => 'WIDGET_VISITOR',
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'total_tokens' => 0,
            'cost' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $allowOrigin = '*';
        if ($widget->target_domain && $origin) {
            $allowOrigin = rtrim($origin, '/');
        }
        
        // Send to Python AI Webhook
        try {
            $aiAgentUrl = env('AI_AGENT_URL', 'http://127.0.0.1:8000') . '/webhook';
            $response = Http::timeout(15)->post($aiAgentUrl, [
                'sender' => $sessionId,
                'message' => $messageText,
                'department_id' => $department->id,
                'pushname' => $name,
                'is_triggered' => true
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'status' => 'success',
                    'ai_reply' => $data['ai_reply'] ?? null
                ])->header('Access-Control-Allow-Origin', $allowOrigin)
                  ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                  ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            }
        } catch (\Exception $e) {
            Log::error("Livechat AI Agent error: " . $e->getMessage());
        }
        
        return response()->json([
            'status' => 'success',
            'ai_reply' => null
        ])->header('Access-Control-Allow-Origin', $allowOrigin)
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    }
}
