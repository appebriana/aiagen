<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\KnowledgeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CmsController extends Controller
{
    /**
     * Normalize WhatsApp JID: strip @lid, @s.whatsapp.net, @c.us suffixes.
     * Keeps @g.us (group) intact.
     */
    private function normalizePhone($phone)
    {
        if (!$phone) return $phone;
        if (str_contains($phone, '@g.us')) return $phone;
        return preg_replace('/@(s\.whatsapp\.net|c\.us|lid)$/i', '', $phone);
    }
    public function index(Request $request, $departmentId = null)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        $selectedUserId = $request->get('user_id');

        // 1. Ambil daftar user untuk filter Admin
        $users = [];
        if ($isAdmin) {
            $users = \App\Models\User::where('role', 'pengguna')->get();
        }

        // 2. Ambil daftar departemen untuk sidebar
        if ($isAdmin) {
            if ($selectedUserId) {
                $departments = Department::with('user')->where('user_id', $selectedUserId)->get();
            } else {
                $departments = collect(); // Kosongkan jika belum pilih user
            }
        } else {
            $departments = Department::where('user_id', $user->id)->get();
        }

        // 3. Tentukan departemen yang sedang aktif
        $activeDepartment = null;
        if ($departmentId) {
            $activeDepartment = Department::find($departmentId);
        } elseif ($departments->isNotEmpty()) {
            // Jika ada filter user, atau bukan admin, ambil yang pertama
            $activeDepartment = $departments->first();
        }

        // 4. Ambil daftar chat unik (pelanggan) untuk departemen ini
        $conversations = [];
        if ($activeDepartment) {
            $conversations = DB::table('ai_chat_logs')
                ->select(
                    DB::raw("REPLACE(REPLACE(REPLACE(customer_phone, '@s.whatsapp.net', ''), '@c.us', ''), '@lid', '') as customer_phone"),
                    DB::raw('MAX(created_at) as last_chat')
                )
                ->where('department_id', $activeDepartment->id)
                ->groupBy(DB::raw("REPLACE(REPLACE(REPLACE(customer_phone, '@s.whatsapp.net', ''), '@c.us', ''), '@lid', '')"))
                ->orderBy('last_chat', 'desc')
                ->get();

            // Ambil info detail customer (nama & is_ai_enabled)
            foreach ($conversations as $conv) {
                $normalizedPhone = $this->normalizePhone($conv->customer_phone);
                $customer = DB::table('customers')
                    ->where('user_id', $activeDepartment->user_id)
                    ->where(function($q) use ($normalizedPhone) {
                        $q->where('phone', $normalizedPhone)
                          ->orWhere('phone', $normalizedPhone . '@s.whatsapp.net')
                          ->orWhere('phone', $normalizedPhone . '@c.us')
                          ->orWhere('phone', $normalizedPhone . '@lid');
                    })
                    ->first();
                
                $conv->customer_name = $customer ? ($customer->nickname ?: $customer->name) : 'Unknown';
                $conv->is_ai_enabled = $customer ? $customer->is_ai_enabled : true;
                
                // Ambil snippet pesan terakhir
                $lastMsg = DB::table('ai_chat_logs')
                    ->where(function($q) use ($normalizedPhone) {
                        $q->where('customer_phone', $normalizedPhone)
                          ->orWhere('customer_phone', $normalizedPhone . '@s.whatsapp.net')
                          ->orWhere('customer_phone', $normalizedPhone . '@c.us')
                          ->orWhere('customer_phone', $normalizedPhone . '@lid');
                    })
                    ->where('department_id', $activeDepartment->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $conv->last_message = $lastMsg ? $lastMsg->question : '';
            }
        }

        // 4. Ambil daftar Device untuk masing-masing platform (jika ada)
        $whatsappDevices = collect();
        $telegramDevices = collect();
        $instagramDevices = collect();
        $facebookDevices = collect();

        if ($activeDepartment) {
            $whatsappDevices = DB::table('whatsapp_devices')
                ->where('department_id', $activeDepartment->id)
                ->where('status', 'connected')
                ->get();
        }

        $viewPath = $isAdmin ? 'admin.cms.index' : 'pengguna.cms.index';
        return view($viewPath, compact(
            'departments', 
            'activeDepartment', 
            'conversations', 
            'whatsappDevices', 
            'telegramDevices', 
            'instagramDevices', 
            'facebookDevices',
            'users',
            'selectedUserId'
        ));
    }

    public function getConversations($departmentId)
    {
        $activeDepartment = Department::find($departmentId);
        if (!$activeDepartment) return response()->json(['status' => 'error', 'message' => 'Dept not found']);

        $conversations = DB::table('ai_chat_logs')
            ->select(
                DB::raw("REPLACE(REPLACE(REPLACE(customer_phone, '@s.whatsapp.net', ''), '@c.us', ''), '@lid', '') as customer_phone"),
                DB::raw('MAX(created_at) as last_chat')
            )
            ->where('department_id', $activeDepartment->id)
            ->groupBy(DB::raw("REPLACE(REPLACE(REPLACE(customer_phone, '@s.whatsapp.net', ''), '@c.us', ''), '@lid', '')"))
            ->orderBy('last_chat', 'desc')
            ->get();

        foreach ($conversations as $conv) {
            $normalizedPhone = $this->normalizePhone($conv->customer_phone);
            $customer = DB::table('customers')
                ->where('user_id', $activeDepartment->user_id)
                ->where(function($q) use ($normalizedPhone) {
                    $q->where('phone', $normalizedPhone)
                      ->orWhere('phone', $normalizedPhone . '@s.whatsapp.net')
                      ->orWhere('phone', $normalizedPhone . '@c.us')
                      ->orWhere('phone', $normalizedPhone . '@lid');
                })
                ->first();
            
            $conv->customer_name = $customer ? ($customer->nickname ?: $customer->name) : 'Unknown';
            $conv->is_ai_enabled = $customer ? (bool)$customer->is_ai_enabled : true;
            
            $lastMsg = DB::table('ai_chat_logs')
                ->where(function($q) use ($normalizedPhone) {
                    $q->where('customer_phone', $normalizedPhone)
                      ->orWhere('customer_phone', $normalizedPhone . '@s.whatsapp.net')
                      ->orWhere('customer_phone', $normalizedPhone . '@c.us')
                      ->orWhere('customer_phone', $normalizedPhone . '@lid');
                })
                ->where('department_id', $activeDepartment->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $conv->last_message = $lastMsg ? ($lastMsg->answer ?: $lastMsg->question) : '';
            $conv->last_chat_time = \Carbon\Carbon::parse($conv->last_chat)->diffForHumans();
        }

        return response()->json([
            'status' => 'success',
            'data' => $conversations
        ]);
    }

    public function getChats($departmentId, $phone)
    {
        $departmentId = (int)$departmentId;
        $phone = $this->normalizePhone(trim($phone));

        // --- SYNC LABEL REAL-TIME: Cek status HOLD label di WhatsApp ---
        try {
            $dept = Department::find($departmentId);
            if ($dept) {
                $port = 3000 + $departmentId;
                $customer = DB::table('customers')
                    ->where('user_id', $dept->user_id)
                    ->where(function($q) use ($phone) {
                        $q->where('phone', $phone)
                          ->orWhere('phone', $phone . '@s.whatsapp.net')
                          ->orWhere('phone', $phone . '@c.us')
                          ->orWhere('phone', $phone . '@lid');
                    })
                    ->first();

                if ($customer) {
                    $labelResponse = \Illuminate\Support\Facades\Http::timeout(2)
                        ->post("http://127.0.0.1:{$port}/check-label", ['target' => $phone]);
                    
                    if ($labelResponse->successful()) {
                        $isHeldByWA = $labelResponse->json('is_held') ?? false;
                        $heldByLabel = (bool)($customer->held_by_label ?? false);
                        $isAiEnabled = (bool)$customer->is_ai_enabled;

                        // Label HOLD baru ditambahkan di WA → matikan AI
                        if ($isHeldByWA && $isAiEnabled) {
                            DB::table('customers')->where('id', $customer->id)
                                ->update(['is_ai_enabled' => 0, 'held_by_label' => 1, 'updated_at' => DB::raw('NOW()')]);
                        }
                        // Label HOLD dihapus di WA + sebelumnya di-hold oleh label → hidupkan AI
                        elseif (!$isHeldByWA && !$isAiEnabled && $heldByLabel) {
                            DB::table('customers')->where('id', $customer->id)
                                ->update(['is_ai_enabled' => 1, 'held_by_label' => 0, 'updated_at' => DB::raw('NOW()')]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Jangan block loading chat jika sync gagal
        }
        // --- END SYNC ---

        $logs = DB::table('ai_chat_logs')
            ->where('department_id', $departmentId)
            ->where(function($q) use ($phone) {
                $q->where('customer_phone', $phone)
                  ->orWhere('customer_phone', $phone . '@s.whatsapp.net')
                  ->orWhere('customer_phone', $phone . '@c.us')
                  ->orWhere('customer_phone', $phone . '@lid');
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($logs as $log) {
            $log->formatted_time = \Carbon\Carbon::parse($log->created_at)->format('H:i');
        }

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'phone' => 'required',
                'message' => 'required',
            ]);

            $departmentId = (int)$request->department_id;
            $deviceId = $request->device_id;
            $waMessageId = null;
            $phone = $this->normalizePhone(trim($request->phone));
            $messageText = trim($request->message);
            
            // Jika device_id tidak dikirim, coba ambil device pertama yang connected di dept ini
            if (!$deviceId) {
                $device = DB::table('whatsapp_devices')
                    ->where('department_id', $departmentId)
                    ->where('status', 'connected')
                    ->first();
                $deviceId = $device ? $device->id : null;
            }

            // 1. Kirim ke Gateway jika ada device yang aktif
            if ($deviceId) {
                $port = 3000 + $departmentId;
                try {
                    // Cari format nomor asli dari database (bisa @c.us, @lid, atau @s.whatsapp.net)
                    $originalPhone = DB::table('ai_chat_logs')
                        ->where('department_id', $departmentId)
                        ->where(function($q) use ($phone) {
                            $q->where('customer_phone', $phone)
                              ->orWhere('customer_phone', $phone . '@s.whatsapp.net')
                              ->orWhere('customer_phone', $phone . '@c.us')
                              ->orWhere('customer_phone', $phone . '@lid');
                        })
                        ->orderBy('id', 'desc')
                        ->value('customer_phone');
                    
                    // Gunakan format asli jika ditemukan, fallback ke @c.us
                    $target = $originalPhone ?: $phone;
                    if (!str_contains($target, '@')) {
                        $target = str_replace(['+', ' '], '', $target);
                        $target = $target . '@c.us';
                    }

                    \Illuminate\Support\Facades\Log::info("CMS Send: Mengirim ke gateway port {$port}, target: {$target}");

                    $gatewayResponse = \Illuminate\Support\Facades\Http::timeout(3)->post("http://127.0.0.1:{$port}/send", [
                        'target' => $target,
                        'message' => $messageText,
                        'reply_to_msg_id' => $request->reply_to_msg_id
                    ]);

                    if ($gatewayResponse->successful()) {
                        $resData = $gatewayResponse->json();
                        $waMessageId = $resData['message_id'] ?? null;
                        \Illuminate\Support\Facades\Log::info("CMS Send: Gateway berhasil, message_id: {$waMessageId}");
                    } else {
                        \Illuminate\Support\Facades\Log::warning("CMS Send: Gateway error status " . $gatewayResponse->status());
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("CMS Send Gateway Error: " . $e->getMessage());
                }
            } else {
                \Illuminate\Support\Facades\Log::warning("CMS Send: Tidak ada device aktif untuk dept {$departmentId}");
            }

            // 2. SELALU simpan ke database
            \Illuminate\Support\Facades\Log::info("CMS Send: Menyimpan ke DB, phone: {$phone}");
            
            $lastUnanswered = DB::table('ai_chat_logs')
                ->where('department_id', $departmentId)
                ->where(function($q) use ($phone) {
                    $q->where('customer_phone', $phone)
                      ->orWhere('customer_phone', $phone . '@s.whatsapp.net')
                      ->orWhere('customer_phone', $phone . '@c.us')
                      ->orWhere('customer_phone', $phone . '@lid');
                })
                ->where(function($query) {
                    $query->whereNull('answer')->orWhere('answer', '');
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($lastUnanswered) {
                DB::table('ai_chat_logs')
                    ->where('id', $lastUnanswered->id)
                    ->update([
                        'answer' => $messageText,
                        'wa_message_id' => $waMessageId,
                        'model' => 'MANUAL_ADMIN',
                        'updated_at' => DB::raw('NOW()'),
                    ]);
            } else {
                DB::table('ai_chat_logs')->insert([
                    'department_id' => $departmentId,
                    'customer_phone' => $phone,
                    'question' => '[ADMIN MANUAL REPLY]',
                    'answer' => $messageText,
                    'wa_message_id' => $waMessageId,
                    'model' => 'MANUAL_ADMIN',
                    'prompt_tokens' => 0,
                    'completion_tokens' => 0,
                    'total_tokens' => 0,
                    'cost' => 0,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()'),
                ]);
            }

            // 3. Otomatis matikan AI untuk customer ini (Takeover)
            $dept = Department::find($departmentId);
            if ($dept) {
                DB::table('customers')
                    ->where('user_id', $dept->user_id)
                    ->where(function($q) use ($phone) {
                        $q->where('phone', $phone)
                          ->orWhere('phone', $phone . '@s.whatsapp.net')
                          ->orWhere('phone', $phone . '@c.us')
                          ->orWhere('phone', $phone . '@lid');
                    })
                    ->update([
                        'is_ai_enabled' => 0,
                        'updated_at' => DB::raw('NOW()')
                    ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("CMS Send FATAL: " . $e->getMessage() . " | " . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function deleteMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:ai_chat_logs,id',
            'department_id' => 'required|exists:departments,id'
        ]);

        $chat = DB::table('ai_chat_logs')->where('id', $request->chat_id)->first();
        
        if (!$chat || !$chat->wa_message_id) {
            return response()->json(['status' => 'error', 'message' => 'ID Pesan WhatsApp tidak ditemukan'], 400);
        }

        $port = 3000 + $request->department_id;

        try {
            $response = Http::timeout(10)->post("http://127.0.0.1:{$port}/delete-message", [
                'message_id' => $chat->wa_message_id
            ]);

            if ($response->successful()) {
                DB::table('ai_chat_logs')->where('id', $request->chat_id)->update([
                    'answer' => '[PESAN DITARIK]',
                    'updated_at' => now()
                ]);
                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'error', 'message' => 'Gagal menarik pesan di WhatsApp'], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
