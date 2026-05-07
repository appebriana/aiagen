<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    /**
     * Check if a username is available.
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'ignore_id' => ['nullable', 'integer'],
        ]);

        $query = User::where('username', $request->username);

        if ($request->ignore_id) {
            $query->where('id', '!=', $request->ignore_id);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Username sudah digunakan.' : 'Username tersedia.',
        ]);
    }

    /**
     * Check if an email is available.
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'ignore_id' => ['nullable', 'integer'],
        ]);

        $query = User::where('email', $request->email);

        if ($request->ignore_id) {
            $query->where('id', '!=', $request->ignore_id);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email sudah digunakan.' : 'Email tersedia.',
        ]);
    }
}
