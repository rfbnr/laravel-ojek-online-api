<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengemudi;
use App\Models\Ulasan;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        try {
            $user = $request->user();
            $currentAccessToken = $user->currentAccessToken();

            if (!$currentAccessToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            $validatedData = $request->validate([
                'longitude_jemput' => 'required|string',
                'latitude_jemput' => 'required|string',
                'longitude_tujuan' => 'required|string',
                'latitude_tujuan' => 'required|string',
                'id_metode_pembayaran' => 'required|exists:metode_pembayarans,id',
            ]);

            // Find an available driver randomly
            $pengemudi = Pengemudi::where('status_pengemudi', 'aktif')->inRandomOrder()->first();

            if (!$pengemudi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No available drivers found',
                ], 404);
            }

            $user = $request->user();

            if(!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User Unauthorized',
                ], 401);
            }

            $order = Order::create([
                'id_user' => $user->id,
                'id_pengemudi' => $pengemudi->id,
                'longitute_jemput' => $validatedData['longitude_jemput'],
                'latitude_jemput' => $validatedData['latitude_jemput'],
                'longitute_tujuan' => $validatedData['longitude_tujuan'],
                'latitude_tujuan' => $validatedData['latitude_tujuan'],
                'waktu_order' => now(),
                'waktu_terima_order' => null,
                'waktu_jemput' => null,
                'waktu_sampai' => null,
                'total_harga' => rand(10000, 100000), // Random price between 10,000 and 100,000
                'harga_bersih' => rand(5000, 50000), // Random price between 5,000 and 50,000
                'status_perjalanan' => 'menunggu',
                'id_metode_pembayaran' => $validatedData['id_metode_pembayaran'],
            ]);

            // Update driver status
            $pengemudi->update(['status_pengemudi' => 'on-trip']);

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function acceptOrder(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            if($order->id_pengemudi !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to accept this order',
                ], 403);
            }

            if ($order->status_perjalanan !== 'menunggu') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order cannot be accepted',
                ], 400);
            }

            $order->update([
                'waktu_terima_order' => now(),
                'status_perjalanan' => 'diperjalanan',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order accepted successfully',
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while accepting the order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function pickupOrder(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            if($order->id_pengemudi !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to pick up this order',
                ], 403);
            }

            if ($order->status_perjalanan !== 'diperjalanan') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order cannot be picked up',
                ], 400);
            }

            $order->update([
                'waktu_jemput' => now(),
                'status_perjalanan' => 'dijemput',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order picked up successfully',
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while picking up the order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function completeOrder(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            if($order->id_pengemudi !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to complete this order',
                ], 403);
            }

            if ($order->status_perjalanan !== 'dijemput') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order cannot be completed',
                ], 400);
            }

            $order->update([
                'waktu_sampai' => now(),
                'status_perjalanan' => 'selesai',
            ]);

            // Update driver status to available
            $pengemudi = Pengemudi::findOrFail($order->id_pengemudi);
            $pengemudi->update(['status_pengemudi' => 'aktif']);

            return response()->json([
                'status' => 'success',
                'message' => 'Order completed successfully',
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while completing the order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function rateOrder(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'komentar' => 'nullable|string',
            ]);

            $order = Order::findOrFail($id);

            if ($order->id_user !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to rate this order',
                ], 403);
            }

            $ulasan = Ulasan::create([
                'id_order' => $order->id,
                'id_user' => $request->user()->id,
                'rating' => $validatedData['rating'],
                'komentar' => $validatedData['komentar'],
                'waktu_ulasan' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Rating submitted successfully',
                'data' => $ulasan,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while submitting the rating',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function orderHistory()
    {
        try {
            $orders = Order::where('id_user', Auth::id())->where('status_perjalanan', 'selesai')->with('pengemudi', 'ulasan')->get();

            if($orders->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No order history status selesai found',
                    'data' => [],
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order history status selesai fetched successfully',
                'data' => $orders,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the order history',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
