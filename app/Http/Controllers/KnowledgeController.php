<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeFile;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class KnowledgeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $isAdmin = auth()->user()->isAdmin();

        if ($isAdmin) {
            $files = KnowledgeFile::with(['user', 'department'])->latest()->get();
        } else {
            $files = KnowledgeFile::where('user_id', $userId)->with('department')->latest()->get();
        }

        $departments = Department::where('user_id', $userId)->get();

        return view('admin.ai-agen.knowledge', compact('files', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,txt|max:5120',
            'department_id' => 'required|exists:departments,id',
        ]);

        $userId = auth()->id();
        $deptId = $request->department_id;
        
        // Cek kepemilikan departemen
        $dept = Department::where('id', $deptId)->where('user_id', $userId)->first();
        if (!$dept) abort(403);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        
        // Simpan ke direktori ai-agent/knowledge/{department_id}
        // Kita gunakan department_id agar AI bisa membedakan pengetahuan per departemen
        $destinationPath = base_path('ai-agent/knowledge/' . $deptId);
        
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);

        KnowledgeFile::create([
            'user_id' => $userId,
            'department_id' => $deptId,
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => 'ai-agent/knowledge/' . $deptId . '/' . $fileName,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => File::size($destinationPath . '/' . $fileName),
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah ke Departemen ' . $dept->name);
    }

    public function download(KnowledgeFile $knowledgeFile)
    {
        if ($knowledgeFile->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $filePath = base_path($knowledgeFile->file_path);

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath, $knowledgeFile->original_name);
    }

    public function destroy(KnowledgeFile $knowledgeFile)
    {
        if ($knowledgeFile->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $filePath = base_path($knowledgeFile->file_path);
        
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $knowledgeFile->delete();

        return redirect()->back()->with('success', 'File berhasil dihapus.');
    }
}
