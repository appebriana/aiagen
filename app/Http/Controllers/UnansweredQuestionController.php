<?php

namespace App\Http\Controllers;

use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnansweredQuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = UnansweredQuestion::with('department')->orderBy('created_at', 'desc');
        $users = [];

        // Jika super admin, ambil daftar pengguna untuk pilihan filter
        if (Auth::user()->role === 'admin') {
            $users = \App\Models\User::where('role', 'pengguna')->orderBy('name')->get();
            
            // Admin HARUS pilih user_id agar data muncul (sesuai permintaan user)
            if ($request->filled('user_id')) {
                $selectedUserId = $request->user_id;
                $query->whereHas('department', function($q) use ($selectedUserId) {
                    $q->where('user_id', $selectedUserId);
                });
            } else {
                // Jika admin belum pilih user, kembalikan koleksi kosong
                $questions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
                return view('admin.unanswered.index', compact('questions', 'users'));
            }
        }

        // Filter: Pencarian (Pertanyaan atau Nomor Pengirim)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('sender', 'like', "%{$search}%");
            });
        }

        // Filter: Rentang Tanggal
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        // Filter: Status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_answered', false);
            } elseif ($request->status === 'answered') {
                $query->where('is_answered', true);
            }
        }

        // Jika bukan super admin, filter berdasarkan department yang dimiliki user
        if (Auth::user()->role !== 'admin') {
            $query->whereHas('department', function($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $questions = $query->paginate(20)->withQueryString();

        return view('admin.unanswered.index', compact('questions', 'users'));
    }

    public function update(Request $request, UnansweredQuestion $unansweredQuestion)
    {
        $request->validate([
            'answer' => 'required|string'
        ]);

        $unansweredQuestion->update([
            'answer' => $request->answer,
            'is_answered' => true
        ]);

        return redirect()->back()->with('success', 'Jawaban berhasil disimpan dan akan digunakan oleh AI.');
    }

    public function destroy(UnansweredQuestion $unansweredQuestion)
    {
        $unansweredQuestion->delete();
        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Bulk Delete questions.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih.'], 400);
        }

        UnansweredQuestion::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => count($ids) . ' pertanyaan berhasil dihapus.']);
    }

    /**
     * Export to PDF with active filters.
     */
    public function exportPdf(Request $request)
    {
        // Pastikan library terinstall, jika tidak beri instruksi
        if (!class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            return redirect()->back()->with('error', 'Fitur PDF memerlukan library dompdf. Silakan perbaiki koneksi SSL dan jalankan: composer require barryvdh/laravel-dompdf');
        }

        $query = UnansweredQuestion::with('department')->orderBy('created_at', 'desc');

        // Terapkan filter yang sama dengan index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('sender', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }
        if ($request->filled('status')) {
            if ($request->status === 'pending') $query->where('is_answered', false);
            elseif ($request->status === 'answered') $query->where('is_answered', true);
        }

        if (Auth::user()->role === 'admin') {
            if ($request->filled('user_id')) {
                $selectedUserId = $request->user_id;
                $query->whereHas('department', function($q) use ($selectedUserId) {
                    $q->where('user_id', $selectedUserId);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            $query->whereHas('department', function($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $questions = $query->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.unanswered.pdf', compact('questions'));
        return $pdf->download('laporan-pertanyaan-' . now()->format('Y-m-d') . '.pdf');
    }
}
