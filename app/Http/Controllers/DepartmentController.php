<?php
namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = auth()->user()->isAdmin();
        $query = Department::query();

        if ($isAdmin) {
            $query->with('user');
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            if ($request->has('user_id') && $request->user_id != '') {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $query->where('user_id', auth()->id());
        }

        $departments = $query->withCount(['knowledgeFiles', 'whatsappDevices'])->latest()->get();
        $users = $isAdmin ? \App\Models\User::all() : collect();

        return view('admin.ai-agen.departments', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $isAdmin = auth()->user()->isAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => $isAdmin ? 'required|exists:users,id' : 'nullable',
            'ai_name' => 'nullable|string|max:255',
            'ai_job_description' => 'nullable|string',
            'reply_to_groups' => 'nullable',
            'is_24_hours' => 'nullable',
            'open_time' => 'nullable',
            'close_time' => 'nullable',
            'is_csat_enabled' => 'nullable',
            'tone_of_voice' => 'nullable|string|in:casual,formal,technical',
        ]);

        Department::create([
            'user_id' => $isAdmin ? $request->user_id : auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'ai_name' => $request->ai_name,
            'ai_job_description' => $request->ai_job_description,
            'reply_to_groups' => $request->has('reply_to_groups'),
            'is_24_hours' => $request->has('is_24_hours'),
            'open_time' => $request->open_time,
            'close_time' => $request->close_time,
            'is_csat_enabled' => $request->has('is_csat_enabled'),
            'tone_of_voice' => $request->tone_of_voice ?? 'casual',
        ]);

        return redirect()->back()->with('success', 'Departemen berhasil dibuat.');
    }

    public function update(Request $request, Department $department)
    {
        $isAdmin = auth()->user()->isAdmin();
        
        if (!$isAdmin && $department->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => $isAdmin ? 'required|exists:users,id' : 'nullable',
            'ai_name' => 'nullable|string|max:255',
            'ai_job_description' => 'nullable|string',
            'reply_to_groups' => 'nullable',
            'is_24_hours' => 'nullable',
            'open_time' => 'nullable',
            'close_time' => 'nullable',
            'is_csat_enabled' => 'nullable',
            'tone_of_voice' => 'nullable|string|in:casual,formal,technical',
        ]);

        $department->update([
            'user_id' => $isAdmin ? $request->user_id : $department->user_id,
            'name' => $request->name,
            'description' => $request->description,
            'ai_name' => $request->ai_name,
            'ai_job_description' => $request->ai_job_description,
            'reply_to_groups' => $request->has('reply_to_groups'),
            'is_24_hours' => $request->has('is_24_hours'),
            'open_time' => $request->open_time,
            'close_time' => $request->close_time,
            'is_csat_enabled' => $request->has('is_csat_enabled'),
            'tone_of_voice' => $request->tone_of_voice ?? 'casual',
        ]);

        return redirect()->back()->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        if (!auth()->user()->isAdmin() && $department->user_id !== auth()->id()) {
            abort(403);
        }

        $department->delete();
        return redirect()->back()->with('success', 'Departemen berhasil dihapus.');
    }
}
