<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Resolve department IDs based on user role.
     * Admin: uses the selected user_id from query, or shows all users to pick.
     * Pengguna: always scoped to own departments.
     */
    private function resolveDepartmentIds(Request $request, &$selectedUser)
    {
        $user = Auth::user();
        $selectedUser = null;

        if ($user->isAdmin()) {
            $selectedUserId = $request->get('user_id');
            if ($selectedUserId) {
                $selectedUser = User::find($selectedUserId);
                if ($selectedUser) {
                    return Department::where('user_id', $selectedUser->id)->pluck('id');
                }
            }
            // If no user selected, return empty collection (admin must pick a user)
            return collect();
        }

        // Pengguna: always own departments
        $selectedUser = $user;
        return Department::where('user_id', $user->id)->pluck('id');
    }

    public function interaction(Request $request)
    {
        $user = Auth::user();
        $range = $request->get('range', 'harian'); // harian, mingguan, bulanan, tahunan
        $type = $request->get('type', 'personal'); // personal, grup

        // For admin: get list of pengguna users for the dropdown
        $penggunaUsers = null;
        if ($user->isAdmin()) {
            $penggunaUsers = User::where('role', 'pengguna')->orderBy('name')->get();
        }

        $selectedUser = null;
        $departmentIds = $this->resolveDepartmentIds($request, $selectedUser);

        // If admin hasn't selected a user yet, show the page with empty data
        if ($user->isAdmin() && !$selectedUser) {
            $stats = ['labels' => [], 'counts' => []];
            $topInteractions = collect();
            return view('pengguna.laporan.interaksi', compact('stats', 'topInteractions', 'range', 'type', 'penggunaUsers', 'selectedUser'));
        }

        $query = DB::table('ai_chat_logs')
            ->whereIn('department_id', $departmentIds);

        if ($type === 'grup') {
            $query->where('customer_phone', 'like', '%@g.us');
        } else {
            $query->where('customer_phone', 'not like', '%@g.us');
        }

        // Statistik untuk Grafik
        $stats = $this->getStatsData($query, $range);

        // List Top Interaksi (Top 10)
        $topInteractions = DB::table('ai_chat_logs')
            ->select('customer_phone', DB::raw('count(*) as total'))
            ->whereIn('department_id', $departmentIds)
            ->when($type === 'grup', function($q) {
                return $q->where('customer_phone', 'like', '%@g.us');
            }, function($q) {
                return $q->where('customer_phone', 'not like', '%@g.us');
            })
            ->groupBy('customer_phone')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Cari nama customer dari tabel customers jika ada
        $targetUserId = $selectedUser->id;
        foreach ($topInteractions as $item) {
            $customer = DB::table('customers')
                ->where('user_id', $targetUserId)
                ->where('phone', $item->customer_phone)
                ->first();
            $item->name = $customer ? ($customer->nickname ?: $customer->name) : 'Unknown';
        }

        return view('pengguna.laporan.interaksi', compact('stats', 'topInteractions', 'range', 'type', 'penggunaUsers', 'selectedUser'));
    }

    private function getStatsData($query, $range)
    {
        $data = [];
        $labels = [];
        $counts = [];

        if ($range === 'harian') {
            $results = (clone $query)
                ->whereDate('created_at', Carbon::today())
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as total'))
                ->groupBy('hour')
                ->pluck('total', 'hour')
                ->all();

            for ($i = 0; $i < 24; $i++) {
                $labels[] = sprintf("%02d:00", $i);
                $counts[] = $results[$i] ?? 0;
            }
        } elseif ($range === 'mingguan') {
            $start = Carbon::now()->startOfWeek();
            $results = (clone $query)
                ->where('created_at', '>=', $start)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->pluck('total', 'date')
                ->all();

            for ($i = 0; $i < 7; $i++) {
                $date = $start->copy()->addDays($i);
                $labels[] = $date->format('d M');
                $counts[] = $results[$date->toDateString()] ?? 0;
            }
        } elseif ($range === 'bulanan') {
            $start = Carbon::now()->startOfMonth();
            $results = (clone $query)
                ->where('created_at', '>=', $start)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->pluck('total', 'date')
                ->all();

            $daysInMonth = Carbon::now()->daysInMonth;
            for ($i = 0; $i < $daysInMonth; $i++) {
                $date = $start->copy()->addDays($i);
                $labels[] = $date->format('d');
                $counts[] = $results[$date->toDateString()] ?? 0;
            }
        } elseif ($range === 'tahunan') {
            $results = (clone $query)
                ->whereYear('created_at', Carbon::now()->year)
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->all();

            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create()->month($i)->format('M');
                $counts[] = $results[$i] ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'counts' => $counts
        ];
    }

    public function interactionDetail(Request $request, $phone)
    {
        $user = Auth::user();
        $selectedUser = null;
        $departmentIds = $this->resolveDepartmentIds($request, $selectedUser);

        if ($departmentIds->isEmpty()) {
            return response()->json(['status' => 'error', 'data' => []]);
        }

        $range = $request->get('range', 'harian');
        
        $query = DB::table('ai_chat_logs')
            ->whereIn('department_id', $departmentIds)
            ->where('customer_phone', $phone);

        // Apply Time Filter
        if ($range === 'harian') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($range === 'mingguan') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($range === 'bulanan') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        } elseif ($range === 'tahunan') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        foreach ($logs as $log) {
            $log->formatted_date = Carbon::parse($log->created_at)->format('d M Y H:i');
            // Bersihkan tag internal seperti [[SET_NAME: ...]]
            $log->answer = preg_replace('/\[\[.*?\]\]/', '', $log->answer);
            $log->answer = trim($log->answer);
        }

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $selectedUser = null;
        $departmentIds = $this->resolveDepartmentIds($request, $selectedUser);

        if ($departmentIds->isEmpty()) {
            return back()->with('error', 'Silakan pilih pengguna terlebih dahulu.');
        }

        $range = $request->get('range', 'harian');
        $type = $request->get('type', 'personal');
        
        $query = DB::table('ai_chat_logs')->whereIn('department_id', $departmentIds);

        // Filter Type (Personal/Grup)
        if ($type === 'grup') {
            $query->where('customer_phone', 'LIKE', '%@g.us');
        } else {
            $query->where('customer_phone', 'NOT LIKE', '%@g.us');
        }

        // Filter Range
        if ($range === 'harian') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($range === 'mingguan') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($range === 'bulanan') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        } elseif ($range === 'tahunan') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        foreach ($logs as $log) {
            $log->answer = preg_replace('/\[\[.*?\]\]/', '', $log->answer);
            $log->answer = trim($log->answer);
        }

        $fileName = 'Laporan_' . ucfirst($type) . '_' . ucfirst($range) . '_' . date('Y-m-d') . '.xls';
        
        $headers = array(
            "Content-Type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $output = view('pengguna.laporan.excel_interaksi', compact('logs'))->render();

        return response($output, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $selectedUser = null;
        $departmentIds = $this->resolveDepartmentIds($request, $selectedUser);

        if ($departmentIds->isEmpty()) {
            return back()->with('error', 'Silakan pilih pengguna terlebih dahulu.');
        }

        $range = $request->get('range', 'harian');
        $type = $request->get('type', 'personal');
        
        $query = DB::table('ai_chat_logs')->whereIn('department_id', $departmentIds);

        // Filter Type
        if ($type === 'grup') {
            $query->where('customer_phone', 'LIKE', '%@g.us');
        } else {
            $query->where('customer_phone', 'NOT LIKE', '%@g.us');
        }

        // Filter Range
        if ($range === 'harian') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($range === 'mingguan') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($range === 'bulanan') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        } elseif ($range === 'tahunan') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        $logs = $query->orderBy('created_at', 'desc')->limit(200)->get();

        foreach ($logs as $log) {
            $log->answer = preg_replace('/\[\[.*?\]\]/', '', $log->answer);
            $log->answer = trim($log->answer);
        }

        $pdf = Pdf::loadView('pengguna.laporan.pdf_interaksi', compact('logs', 'user', 'range', 'type'));
        return $pdf->setPaper('a4', 'landscape')->download('Laporan_' . ucfirst($type) . '_' . ucfirst($range) . '.pdf');
    }

    /**
     * Coming soon page for platforms not yet implemented.
     */
    public function comingSoon(Request $request)
    {
        $platform = 'Platform';
        $routeName = $request->route()->getName();
        
        if (str_contains($routeName, '.ig')) {
            $platform = 'Instagram';
        } elseif (str_contains($routeName, '.telegram')) {
            $platform = 'Telegram';
        }

        return view('pengguna.laporan.coming_soon', compact('platform'));
    }
}
