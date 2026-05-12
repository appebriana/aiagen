<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas feedback Anda! Kami akan meninjau saran Anda segera.',
        ]);
    }

    // --- Admin Methods ---

    public function index()
    {
        $feedbacks = Feedback::with('user')->orderBy('created_at', 'desc')->paginate(15);
        $stats = [
            'total' => Feedback::count(),
            'draf' => Feedback::where('status', 'draf')->count(),
            'proses' => Feedback::where('status', 'proses')->count(),
            'selesai' => Feedback::where('status', 'selesai')->count(),
        ];
        return view('admin.feedback.index', compact('feedbacks', 'stats'));
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $request->validate([
            'status' => 'required|in:draf,proses,selesai',
        ]);

        $feedback->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Status masukan berhasil diperbarui.');
    }
}
