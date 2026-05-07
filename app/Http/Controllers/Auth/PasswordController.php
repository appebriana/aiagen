<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',
                'max:18',
                'regex:/[a-z]/',      // minimal 1 huruf kecil
                'regex:/[A-Z]/',      // minimal 1 huruf kapital
                'regex:/[0-9]/',      // minimal 1 angka
                'regex:/[@$!%*#?&^()_\-+=\[\]{}|\\:;"\'<>,.\/ ~`]/', // minimal 1 simbol
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.max' => 'Password maksimal 18 karakter.',
            'password.regex' => 'Password harus mengandung huruf kecil, huruf kapital, angka, dan simbol.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
