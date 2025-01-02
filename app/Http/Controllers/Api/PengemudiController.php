<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengemudi;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;


class PengemudiController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_pengemudi' => 'required|string|max:255',
            'email_pengemudi' => 'required|email|unique:pengemudis,email_pengemudi',
            'phone_pengemudi' => 'required|numeric|unique:pengemudis,phone_pengemudi',
            'password' => 'required|string|min:6',
            'merek_tipe_kendaraan' => 'required|string',
            'plat_nomor' => 'required|string|unique:pengemudis,plat_nomor',
        ]);

        $driver = Pengemudi::create([
            'nama_pengemudi' => $validated['nama_pengemudi'],
            'email_pengemudi' => $validated['email_pengemudi'],
            'phone_pengemudi' => $validated['phone_pengemudi'],
            'password' => Hash::make($validated['password']),
            'merek_tipe_kendaraan' => $validated['merek_tipe_kendaraan'],
            'plat_nomor' => $validated['plat_nomor'],
            'tgl_registrasi' => now(),
            'status_pengemudi' => 'nonaktif',
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Driver created successfully',
                'data' => $driver,
            ],
            201
        );
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email_pengemudi' => 'required|email',
            'password' => 'required|string',
        ]);

        $driver = Pengemudi::where('email_pengemudi', $validated['email_pengemudi'])->first();

        if (!$driver || !Hash::check($validated['password'], $driver->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $driver->createToken('driver_token')->plainTextToken;

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Driver logged in successfully',
                'data' => $driver,
                'token' => $token,
            ],
            200
        );
    }

    public function logout(Request $request) {
        try {
            $pengemudi = $request->user();
            $currentAccessToken = $pengemudi->currentAccessToken();

            if (!$currentAccessToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active access token found',
                ], 400);
            }

            $isTokenRevoked = $pengemudi->tokens()->where('id', $currentAccessToken->id)->delete();

            if ($isTokenRevoked > 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Logged out driver successfully',
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

    public function activateDriver(Request $request, $id)
    {
        $driver = Pengemudi::find($id);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        $driver->update(['status_pengemudi' => 'aktif']);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Driver activated successfully',
                'data' => $driver,
            ],
            200
        );
    }
}
