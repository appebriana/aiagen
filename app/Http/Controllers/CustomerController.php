<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function toggleMute(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'user_id' => 'required'
        ]);

        $phone = $request->phone;
        $userId = $request->user_id;

        // Pastikan hanya pemilik atau admin yang bisa mute
        if ($userId != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $customer = Customer::firstOrCreate(
            ['phone' => $phone, 'user_id' => $userId],
            ['name' => 'Pelanggan Baru']
        );

        $customer->update([
            'is_muted' => !$customer->is_muted
        ]);

        $status = $customer->is_muted ? 'AI dimatikan untuk pelanggan ini.' : 'AI diaktifkan kembali.';
        
        return redirect()->back()->with('success', $status);
    }
}
