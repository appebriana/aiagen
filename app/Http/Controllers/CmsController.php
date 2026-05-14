<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\KnowledgeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CmsController extends Controller
{
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
                ->select('customer_phone', DB::raw('MAX(created_at) as last_chat'))
                ->where('department_id', $activeDepartment->id)
                ->groupBy('customer_phone')
                ->orderBy('last_chat', 'desc')
                ->get();

            // Ambil info detail customer (nama & is_ai_enabled)
            foreach ($conversations as $conv) {
                $customer = DB::table('customers')
                    ->where('user_id', $activeDepartment->user_id)
                    ->where('phone', $conv->customer_phone)
                    ->first();
                
                $conv->customer_name = $customer ? ($customer->nickname ?: $customer->name) : 'Unknown';
                $conv->is_ai_enabled = $customer ? $customer->is_ai_enabled : true;
                
                // Ambil snippet pesan terakhir
                $lastMsg = DB::table('ai_chat_logs')
                    ->where('customer_phone', $conv->customer_phone)
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
            ->select('customer_phone', DB::raw('MAX(created_at) as last_chat'))
            ->where('department_id', $activeDepartment->id)
            ->groupBy('customer_phone')
            ->orderBy('last_chat', 'desc')
            ->get();

        foreach ($conversations as $conv) {
            $customer = DB::table('customers')
                ->where('user_id', $activeDepartment->user_id)
                ->where('phone', $conv->customer_phone)
                ->first();
            
            $conv->customer_name = $customer ? ($customer->nickname ?: $customer->name) : 'Unknown';
            $conv->is_ai_enabled = $customer ? (bool)$customer->is_ai_enabled : true;
            
            $lastMsg = DB::table('ai_chat_logs')
                ->where('customer_phone', $conv->customer_phone)
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
        $phone = trim($phone);

        $logs = DB::table('ai_chat_logs')
            ->where('department_id', $departmentId)
            ->where('customer_phone', $phone)
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
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'phone' => 'required',
            'message' => 'required',
            'device_id' => 'nullable|exists:whatsapp_devices,id'
        ]);

        $departmentId = (int)$request->department_id;
        $deviceId = $request->device_id;
        $phone = trim($request->phone);
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
                // Konversi format nomor telepon jika perlu (gateway biasanya butuh @c.us)
                $target = $phone;
                if (!str_contains($target, '@')) {
                    $target = str_replace(['+', ' '], '', $target);
                    $target = $target . '@c.us';
                }

                \Illuminate\Support\Facades\Http::timeout(5)->post("http://127.0.0.1:{$port}/send", [
                    'target' => $target,
                    'message' => $messageText
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("CMS Send Error: " . $e->getMessage());
                // Tetap lanjut simpan ke DB agar ada history, tapi mungkin pesan aslinya gagal terkirim
            }
        }
        
        // 3. Logika "Smart Pairing": Cek apakah ada pesan pelanggan terakhir yang belum dijawab
        $lastUnanswered = DB::table('ai_chat_logs')
            ->where('department_id', $departmentId)
            ->where('customer_phone', $phone)
            ->where(function($query) {
                $query->whereNull('answer')->orWhere('answer', '');
            })
            ->orderBy('id', 'desc')
            ->first();

        if ($lastUnanswered) {
            // Update pesan yang menggantung agar sejajar di tampilan
            DB::table('ai_chat_logs')
                ->where('id', $lastUnanswered->id)
                ->update([
                    'answer' => $messageText,
                    'model' => 'MANUAL_ADMIN',
                    'updated_at' => DB::raw('NOW()'),
                ]);
        } else {
            // Jika tidak ada pesan yang menggantung, buat baris baru
            DB::table('ai_chat_logs')->insert([
                'department_id' => $departmentId,
                'customer_phone' => $phone,
                'question' => '[ADMIN MANUAL REPLY]',
                'answer' => $messageText,
                'model' => 'MANUAL_ADMIN',
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'total_tokens' => 0,
                'cost' => 0,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ]);
        }

        // 4. Otomatis matikan AI untuk customer ini (Takeover)
        $dept = Department::find($departmentId);
        if ($dept) {
            DB::table('customers')
                ->where('user_id', $dept->user_id)
                ->where('phone', $phone)
                ->update([
                    'is_ai_enabled' => 0,
                    'updated_at' => DB::raw('NOW()')
                ]);
        }

        return response()->json(['status' => 'success']);
    }
}
