<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and return Sanctum token.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::with('ward')
            ->where('email', $credentials['email'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('smart-vadodara-web')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'ward_id' => $user->ward_id,

                'ward' => $user->ward ? [
                    'id' => $user->ward->id,
                    'ward_no' => $user->ward->ward_no,
                    'name' => $user->ward->name,
                ] : null,
            ],
        ]);
    }

    /**
     * Return currently authenticated user.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('ward');

        return response()->json([
            'success' => true,

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'ward_id' => $user->ward_id,

                'ward' => $user->ward ? [
                    'id' => $user->ward->id,
                    'ward_no' => $user->ward->ward_no,
                    'name' => $user->ward->name,
                ] : null,
            ],
        ]);
    }

    /**
     * Logout current session/token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}