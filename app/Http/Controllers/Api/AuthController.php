<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'required|unique:users,phone',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'tgl_registrasi' => now(),
            'status' => 'aktif',
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user,
            ],
            201
        );
    }

    public function login(Request $request)
    {
        try {
            $loginData = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = \App\Models\User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email not found',
                ], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Incorrect password',
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request) {
        try {
            $user = $request->user();
            $currentAccessToken = $user->currentAccessToken();

            if (!$currentAccessToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active access token found',
                ], 400);
            }

            $isTokenRevoked = $user->tokens()->where('id', $currentAccessToken->id)->delete();

            if ($isTokenRevoked > 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Logged out successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to revoke the access token',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during logout. Please try again later.',
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
            'new_password' => 'required|min:6',
        ]);

        // Cari pengguna berdasarkan nomor telepon
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Password updated successfully',
            ],
            200
        );
    }

    public function getAccountInfo(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengguna tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mengambil informasi akun.',
                'data' => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil informasi akun. Silakan coba lagi nanti.',
            ], 500);
        }
    }
}
