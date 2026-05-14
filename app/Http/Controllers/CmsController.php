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

    public function getChats($departmentId, $phone)
    {
        $logs = DB::table('ai_chat_logs')
            ->where('department_id', $departmentId)
            ->where('customer_phone', $phone)
            ->orderBy('created_at', 'asc')
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
            'message' => 'required'
        ]);

        // Logika pengiriman pesan manual (Takeover)
        // Di sini kita hanya mencatat di DB sebagai simulasi pengiriman
        // Nantinya ini bisa terhubung ke API WhatsApp Gateway
        
        DB::table('ai_chat_logs')->insert([
            'department_id' => $request->department_id,
            'customer_phone' => $request->phone,
            'question' => '[ADMIN MANUAL REPLY]',
            'answer' => $request->message,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Otomatis matikan AI untuk customer ini
        $dept = Department::find($request->department_id);
        DB::table('customers')
            ->where('user_id', $dept->user_id)
            ->where('phone', $request->phone)
            ->update(['is_ai_enabled' => 0]);

        return response()->json(['status' => 'success']);
    }
}
