<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\WhatsappDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $isAdmin = auth()->user()->isAdmin();

        if ($isAdmin) {
            $devices = WhatsappDevice::with(['user', 'department'])->latest()->get();
            $departments = Department::where('user_id', $userId)->get();
        } else {
            $devices = WhatsappDevice::where('user_id', $userId)->with('department')->latest()->get();
            $departments = Department::where('user_id', $userId)->get();
        }

        return view('admin.ai-agen.whatsapp', compact('devices', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $userId = auth()->id();
        
        // Cek kepemilikan departemen
        $dept = Department::where('id', $request->department_id)->where('user_id', $userId)->first();
        if (!$dept) abort(403);

        WhatsappDevice::create([
            'user_id' => $userId,
            'department_id' => $request->department_id,
            'name' => $request->name,
            'status' => 'disconnected',
        ]);

        return redirect()->back()->with('success', 'Device berhasil ditambahkan. Silakan hubungkan (scan QR).');
    }

    public function destroy(WhatsappDevice $whatsappDevice)
    {
        if ($whatsappDevice->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $whatsappDevice->delete();
        return redirect()->back()->with('success', 'Device berhasil dihapus.');
    }

    public function getStatus(WhatsappDevice $device)
    {
        // Secara ideal, setiap device punya URL gateway masing-masing
        // Untuk demo ini, kita asumsikan gateway berjalan di localhost:3000 + ID
        // Misal Dept 1 -> 3000, Dept 2 -> 3001, dst.
        $port = 3000 + ($device->department_id - 1);
        
        try {
            $response = Http::get("http://localhost:{$port}/status");
            return $response->json();
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'Gateway tidak aktif'];
        }
    }
}
