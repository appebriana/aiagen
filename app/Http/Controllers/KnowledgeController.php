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

        if ($isAdmin) {
            $departments = Department::with('user')->get();
        } else {
            $departments = Department::where('user_id', $userId)->get();
        }

        return view('admin.ai-agen.knowledge', compact('files', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:file,url',
            'department_id' => 'required|exists:departments,id',
            'file' => 'required_if:type,file|file|mimes:pdf,txt|max:5120',
            'url' => 'required_if:type,url|nullable|url',
        ]);

        $userId = auth()->id();
        $deptId = $request->department_id;
        
        // Cek kepemilikan departemen
        $dept = Department::where('id', $deptId)->where('user_id', $userId)->first();
        if (!$dept) abort(403);

        if ($request->type === 'file') {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            $destinationPath = base_path('ai-agent/knowledge/' . $deptId);
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);

            KnowledgeFile::create([
                'user_id' => $userId,
                'department_id' => $deptId,
                'type' => 'file',
                'file_name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => 'ai-agent/knowledge/' . $deptId . '/' . $fileName,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => File::size($destinationPath . '/' . $fileName),
            ]);

            return redirect()->back()->with('success', 'Dokumen berhasil diunggah ke Departemen ' . $dept->name);
        } else {
            KnowledgeFile::create([
                'user_id' => $userId,
                'department_id' => $deptId,
                'type' => 'google_sheet',
                'url' => $request->url,
                'file_name' => 'Google Spreadsheet Link',
            ]);

            return redirect()->back()->with('success', 'Link Google Spreadsheet berhasil ditambahkan ke Departemen ' . $dept->name);
        }
    }

    public function download(KnowledgeFile $knowledgeFile)
    {
        if ($knowledgeFile->type !== 'file') {
            return redirect()->back()->with('error', 'Item ini bukan berupa file yang dapat didownload.');
        }

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

        if ($knowledgeFile->type === 'file' && $knowledgeFile->file_path) {
            $filePath = base_path($knowledgeFile->file_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $knowledgeFile->delete();

        return redirect()->back()->with('success', 'Pengetahuan berhasil dihapus.');
    }
}
