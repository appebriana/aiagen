<?php

namespace App\Http\Controllers;

use App\Models\AiChatLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $today = Carbon::today();
        
        // 1. Pesan dijawab AI hari ini
        $messagesToday = AiChatLog::whereDate('created_at', $today)->count();
        
        // 2. Token API per Departemen
        $tokensPerDept = AiChatLog::select('department_id', DB::raw('SUM(total_tokens) as total_tokens'))
            ->with('department')
            ->groupBy('department_id')
            ->get();
            
        // 3. Top 5 Pertanyaan (Normalisasi dikit: trim & lowercase)
        $topQuestions = AiChatLog::select('question', DB::raw('COUNT(*) as count'))
            ->groupBy('question')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
            
        // 4. Data Grafik (Pesan 7 Hari Terakhir)
        $chartData = AiChatLog::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Statistik Tambahan
        $totalUsers = User::count();
        $totalDepartments = Department::count();
        $totalTokens = AiChatLog::sum('total_tokens');

        return view('admin.dashboard', compact(
            'messagesToday', 'tokensPerDept', 'topQuestions', 
            'chartData', 'totalUsers', 'totalDepartments', 'totalTokens'
        ));
    }

    public function penggunaDashboard()
    {
        $userId = auth()->id();
        $today = Carbon::today();
        
        // Ambil ID departemen milik user ini
        $deptIds = Department::where('user_id', $userId)->pluck('id');

        // 1. Pesan dijawab AI hari ini (Khusus Dept User)
        $messagesToday = AiChatLog::whereIn('department_id', $deptIds)
            ->whereDate('created_at', $today)
            ->count();
        
        // 2. Token API per Departemen
        $tokensPerDept = AiChatLog::whereIn('department_id', $deptIds)
            ->select('department_id', DB::raw('SUM(total_tokens) as total_tokens'))
            ->with('department')
            ->groupBy('department_id')
            ->get();
            
        // 3. Top 5 Pertanyaan
        $topQuestions = AiChatLog::whereIn('department_id', $deptIds)
            ->select('question', DB::raw('COUNT(*) as count'))
            ->groupBy('question')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
            
        // 4. Data Grafik (7 Hari Terakhir)
        $chartData = AiChatLog::whereIn('department_id', $deptIds)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $totalDepartments = $deptIds->count();
        $totalTokens = AiChatLog::whereIn('department_id', $deptIds)->sum('total_tokens');

        return view('pengguna.dashboard', compact(
            'messagesToday', 'tokensPerDept', 'topQuestions', 
            'chartData', 'totalDepartments', 'totalTokens'
        ));
    }

    public function aiAgenIndex()
    {
        $userId = auth()->id();
        $deptIds = Department::where('user_id', $userId)->pluck('id');
        
        $stats = [
            'total_interactions' => DB::table('ai_chat_logs')->whereIn('department_id', $deptIds)->count(),
            'today_interactions' => DB::table('ai_chat_logs')->whereIn('department_id', $deptIds)->whereDate('created_at', Carbon::today())->count(),
            'total_departments' => $deptIds->count(),
            'total_knowledge' => DB::table('knowledge_files')->whereIn('department_id', $deptIds)->count(),
        ];

        return view('pengguna.ai-agen.index', compact('stats'));
    }
}
