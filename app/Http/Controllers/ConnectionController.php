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
        $selectedUserId = $request->get('user_id');
        $selectedDeptId = $request->get('department_id');

        if ($isAdmin) {
            $users = \App\Models\User::where('role', 'pengguna')->orderBy('name')->get();
            
            // All departments (unfiltered) for modal form selects
            $allDepartments = Department::with('user')->orderBy('name')->get();

            $deptQuery = Department::query();
            $deviceQuery = WhatsappDevice::with(['user', 'department'])->latest();
            $widgetQuery = \App\Models\LivechatWidget::with(['user', 'department'])->latest();
            
            if ($selectedUserId) {
                $deptQuery->where('user_id', $selectedUserId);
                $deviceQuery->where('user_id', $selectedUserId);
                $widgetQuery->where('user_id', $selectedUserId);
            }
            if ($selectedDeptId) {
                $deptQuery->where('id', $selectedDeptId);
                $deviceQuery->where('department_id', $selectedDeptId);
                $widgetQuery->where('department_id', $selectedDeptId);
            }
            
            $departments = $deptQuery->get();
            $whatsappDevices = $deviceQuery->get();
            $livechatWidgets = $widgetQuery->get();
            
            $filterDeptsQuery = Department::query();
            if ($selectedUserId) {
                $filterDeptsQuery->where('user_id', $selectedUserId);
            }
            $filterDepartments = $filterDeptsQuery->orderBy('name')->get();
        } else {
            $allDepartments = Department::where('user_id', $userId)->with('user')->get();
            $departments = Department::where('user_id', $userId)->get();
            $whatsappDevices = WhatsappDevice::where('user_id', $userId)->with('department')->latest()->get();
            $livechatWidgets = \App\Models\LivechatWidget::where('user_id', $userId)->with('department')->latest()->get();
            $users = collect();
            $filterDepartments = collect();
        }

        return view('admin.ai-agen.connections', compact('whatsappDevices', 'livechatWidgets', 'departments', 'allDepartments', 'tab', 'users', 'filterDepartments'));
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
        $port = 3000 + $device->department_id;
        
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

        $port = 3000 + $device->department_id;
        
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

        $port = 3000 + $device->department_id;
        
        try {
            Http::timeout(5)->post("http://127.0.0.1:{$port}/init");
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gateway tidak merespons']);
        }
    }

    public function storeLivechat(Request $request)
    {
        $isAdmin = auth()->user()->isAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'user_id' => $isAdmin ? 'required|exists:users,id' : 'nullable',
            'livechat_active' => 'required|boolean',
            'livechat_primary_color' => 'required|string|max:7',
            'livechat_welcome_message' => 'nullable|string',
            'target_domain' => 'required|string|max:255',
        ]);

        $userId = $isAdmin ? $request->user_id : auth()->id();

        // Check ownership of department if not admin
        if (!$isAdmin) {
            $dept = Department::where('id', $request->department_id)->where('user_id', $userId)->first();
            if (!$dept) abort(403);
        }

        \App\Models\LivechatWidget::create([
            'user_id' => $userId,
            'department_id' => $request->department_id,
            'name' => $request->name,
            'is_active' => $request->livechat_active,
            'primary_color' => $request->livechat_primary_color,
            'welcome_message' => $request->livechat_welcome_message,
            'target_domain' => $request->target_domain,
        ]);

        return redirect()->route(auth()->user()->role . '.ai-agen.connections.index', ['tab' => 'livechat'])
                         ->with('success', 'Widget Live Chat berhasil ditambahkan.');
     }

     public function updateLivechat(Request $request, \App\Models\LivechatWidget $widget)
     {
         $isAdmin = auth()->user()->isAdmin();
 
         if (!$isAdmin && $widget->user_id !== auth()->id()) {
             abort(403);
         }
         
         $request->validate([
             'name' => 'required|string|max:255',
             'livechat_active' => 'required|boolean',
             'livechat_primary_color' => 'required|string|max:7',
             'livechat_welcome_message' => 'nullable|string',
             'target_domain' => 'required|string|max:255',
         ]);
 
         $widget->update([
             'name' => $request->name,
             'is_active' => $request->livechat_active,
             'primary_color' => $request->livechat_primary_color,
             'welcome_message' => $request->livechat_welcome_message,
             'target_domain' => $request->target_domain,
         ]);

        return redirect()->route(auth()->user()->role . '.ai-agen.connections.index', ['tab' => 'livechat'])
                         ->with('success', 'Konfigurasi Live Chat berhasil diperbarui.');
    }

    public function destroyLivechat(\App\Models\LivechatWidget $widget)
    {
        if (!auth()->user()->isAdmin() && $widget->user_id !== auth()->id()) {
            abort(403);
        }

        $widget->delete();

        return redirect()->route(auth()->user()->role . '.ai-agen.connections.index', ['tab' => 'livechat'])
                         ->with('success', 'Widget Live Chat berhasil dihapus.');
    }
}
