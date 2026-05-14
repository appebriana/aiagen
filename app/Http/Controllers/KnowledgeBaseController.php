<?php

namespace App\Http\Controllers;

use App\Models\UnansweredQuestion;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = Auth::user()->isAdmin();
        $query = UnansweredQuestion::where('is_knowledge', true)->with(['department', 'customer']);

        if ($isAdmin) {
            if ($request->has('user_id') && $request->user_id != '') {
                $departmentIds = Department::where('user_id', $request->user_id)->pluck('id');
                $query->whereIn('department_id', $departmentIds);
            }
        } else {
            $departmentIds = Department::where('user_id', Auth::id())->pluck('id');
            $query->whereIn('department_id', $departmentIds);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%$search%")
                  ->orWhere('answer', 'like', "%$search%");
            });
        }

        $knowledge = $query->latest()->paginate(20)->withQueryString();
        $users = $isAdmin ? User::all() : collect();

        return view('admin.ai-agen.knowledge_base', compact('knowledge', 'users'));
    }

    public function update(Request $request, UnansweredQuestion $unansweredQuestion)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'is_knowledge' => 'required|boolean'
        ]);

        $unansweredQuestion->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'is_knowledge' => $request->is_knowledge
        ]);

        return redirect()->back()->with('success', 'Pengetahuan berhasil diperbarui.');
    }

    public function destroy(UnansweredQuestion $unansweredQuestion)
    {
        $unansweredQuestion->update(['is_knowledge' => false]);
        return redirect()->back()->with('success', 'Pengetahuan dihapus dari memori utama (kembali ke riwayat umum).');
    }

    public function bulkRemove(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih.'], 400);
        }

        UnansweredQuestion::whereIn('id', $ids)->update(['is_knowledge' => false]);

        return response()->json(['success' => true, 'message' => count($ids) . ' pengetahuan berhasil dihapus dari memori utama.']);
    }
}
