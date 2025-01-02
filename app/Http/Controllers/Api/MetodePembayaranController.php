<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MetodePembayaran;

class MetodePembayaranController extends Controller
{
    // create metode pembayaran
    public function store(Request $request)
    {
        $request->validate([
            'nama_metode_pembayaran' => 'required',
        ]);

        $metode_pembayaran = MetodePembayaran::create([
            'nama_metode_pembayaran' => $request->nama_metode_pembayaran,
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Metode Pembayaran created successfully',
                'data' => $metode_pembayaran,
            ],
            201
        );
    }

    // get all metode pembayaran
    public function index()
    {
        $metode_pembayaran = MetodePembayaran::all();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Get all Metode Pembayaran',
                'data' => $metode_pembayaran,
            ],
            200
        );
    }
}
