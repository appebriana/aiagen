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
}
