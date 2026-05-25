<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Subscription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // app/Http/Controllers/Api/DashboardController.php

    public function index()
    {
        return response()->json([
            'stats' => [
                'total_customers'      => Customer::count(),
                'active_subscriptions' => Subscription::where('status', 'active')->count(),
                'total_services'       => Service::count(),
            ],
            'recent_subscriptions' => Subscription::with(['customer', 'service'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($sub) {
                    return [
                        'id'              => $sub->id,
                        'customer_uid'    => $sub->customer->customer_id ?? '-', // CUST26xxx
                        'customer_name'   => $sub->customer->name ?? 'N/A',
                        'service_name'    => $sub->service->name ?? 'N/A',
                        'price_formatted' => 'Rp ' . number_format($sub->service->price ?? 0, 0, ',', '.'),
                        'status'          => $sub->status,
                        'end_date'        => date('d M Y', strtotime($sub->end_date)), // Format tanggal lebih cantik
                    ];
                })
        ]);
    }

}
