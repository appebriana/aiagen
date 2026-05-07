<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\WhatsappDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConnectionController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $isAdmin = auth()->user()->isAdmin();
        $tab = $request->get('tab', 'whatsapp');

        $departments = $isAdmin ? Department::all() : Department::where('user_id', $userId)->get();
        
        if ($isAdmin) {
            $whatsappDevices = WhatsappDevice::with(['user', 'department'])->latest()->get();
            $users = \App\Models\User::all();
        } else {
            $whatsappDevices = WhatsappDevice::where('user_id', $userId)->with('department')->latest()->get();
            $users = collect();
        }

        return view('admin.ai-agen.connections', compact('whatsappDevices', 'departments', 'tab', 'users'));
    }

    // WA Specific Logic
    public function storeWhatsapp(Request $request)
    {
        $isAdmin = auth()->user()->isAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'user_id' => $isAdmin ? 'required|exists:users,id' : 'nullable',
        ]);

        $userId = $isAdmin ? $request->user_id : auth()->id();
        
        // Cek kepemilikan departemen (jika bukan admin)
        if (!$isAdmin) {
            $dept = Department::where('id', $request->department_id)->where('user_id', $userId)->first();
            if (!$dept) abort(403);
        }

        WhatsappDevice::create([
            'user_id' => $userId,
            'department_id' => $request->department_id,
            'name' => $request->name,
            'status' => 'disconnected',
        ]);

        return redirect()->route(auth()->user()->role . '.ai-agen.connections.index', ['tab' => 'whatsapp'])
                         ->with('success', 'Device WhatsApp berhasil ditambahkan.');
    }

    public function destroyWhatsapp(WhatsappDevice $whatsappDevice)
    {
        if ($whatsappDevice->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $whatsappDevice->delete();
        return redirect()->back()->with('success', 'Device WhatsApp berhasil dihapus.');
    }

    public function getWhatsappStatus(WhatsappDevice $device)
    {
        // Ambil status terkini dari DB (sudah disync otomatis oleh Gateway via Python)
        $device->refresh();
        $port = 3000 + ($device->department_id - 1);
        
        $result = [
            'status' => $device->status ?? 'disconnected',
            'qr' => null,
        ];
// ... (rest of the logic)

        // Jika belum connected, coba ambil QR dari Gateway
        if ($device->status !== 'connected') {
            try {
                $response = Http::timeout(3)->get("http://127.0.0.1:{$port}/status");
                $gatewayData = $response->json();
                
                if (isset($gatewayData['status']) && $gatewayData['status'] === 'ready') {
                    // Gateway sudah ready tapi DB belum terupdate, force update
                    $device->update(['status' => 'connected']);
                    $result['status'] = 'connected';
                } elseif (isset($gatewayData['qr'])) {
                    $result['qr'] = $gatewayData['qr'];
                }
            } catch (\Exception $e) {
                // Gateway belum jalan, status tetap dari DB
            }
        }

        return $result;
    }

    public function disconnectWhatsapp(WhatsappDevice $device)
    {
        if ($device->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $port = 3000 + ($device->department_id - 1);
        
        try {
            Http::timeout(5)->post("http://127.0.0.1:{$port}/disconnect");
        } catch (\Exception $e) {
            // Gateway mungkin tidak aktif, tetap update DB
        }

        $device->update(['status' => 'disconnected']);

        return redirect()->back()->with('success', 'Koneksi WhatsApp berhasil diputus. Silakan scan QR baru.');
    }

    public function initWhatsapp(WhatsappDevice $device)
    {
        if ($device->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $port = 3000 + ($device->department_id - 1);
        
        try {
            Http::timeout(5)->post("http://127.0.0.1:{$port}/init");
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gateway tidak merespons']);
        }
    }
}
