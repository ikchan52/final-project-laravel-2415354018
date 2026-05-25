<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        // Load relasi 'customer' dan 'service'
        $query = Subscription::with(['customer', 'service']);

        if ($request->filled('search')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        return SubscriptionResource::collection($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id', // Sesuai tabel subscriptions
            'service_id'  => 'required|exists:services,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:active,inactive,trial,isolir,dismantle',
        ]);

        $subscription = Subscription::create($data); // Aman karena sudah ada di $fillable

        return new SubscriptionResource($subscription->load(['customer', 'service']));
    }

    public function show(Subscription $subscription)
    {
        return new SubscriptionResource($subscription->load(['customer', 'service']));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $data = $request->validate([
            'customer_id' => 'sometimes|required|exists:customers,id',
            'service_id'  => 'sometimes|required|exists:services,id',
            'start_date'  => 'sometimes|required|date',
            'end_date'    => 'sometimes|required|date|after_or_equal:start_date',
            'status'      => 'sometimes|required|in:active,inactive,trial,isolir,dismantle',
        ]);

        $subscription->update($data);

        return (new SubscriptionResource($subscription->load(['customer', 'service'])))
            ->additional(['message' => 'Data langganan berhasil diperbarui']);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'Data langganan berhasil dihapus']);
    }
}
