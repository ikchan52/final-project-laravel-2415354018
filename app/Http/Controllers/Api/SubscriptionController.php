<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Menampilkan daftar semua langganan.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        // Menggunakan eager loading (with) agar data customer dan service ikut terbawa
        $subscriptions = Subscription::with(['customer', 'service'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data langganan',
            'data'    => $subscriptions
        ], 200);
    }

    /**
     * Membuat data langganan baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id', // Pastikan ID pelanggan ada
            'service_id'  => 'required|exists:services,id',  // Pastikan ID layanan ada
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:active,inactive,trial,isolir,dismantle', // Sesuai spek modul
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $subscription = Subscription::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Langganan berhasil dibuat',
            'data'    => $subscription->load(['customer', 'service'])
        ], 201);
    }

    /**
     * Menampilkan detail langganan tertentu.
     */
    public function show(Subscription $subscription)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data langganan',
            'data'    => $subscription->load(['customer', 'service'])
        ], 200);
    }

    /**
     * Memperbarui data langganan.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'sometimes|required|exists:customers,id',
            'service_id'  => 'sometimes|required|exists:services,id',
            'start_date'  => 'sometimes|required|date',
            'end_date'    => 'sometimes|required|date|after_or_equal:start_date',
            'status'      => 'sometimes|required|in:active,inactive,trial,isolir,dismantle',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Update gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $subscription->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data langganan berhasil diperbarui',
            'data'    => $subscription->load(['customer', 'service'])
        ], 200);
    }

    /**
     * Menghapus data langganan.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data langganan berhasil dihapus'
        ], 200);
    }
}
