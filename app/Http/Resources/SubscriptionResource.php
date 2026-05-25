<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // ID asli pelanggan (integer) untuk form
            'customer_id' => $this->customer_id,
            'customer_pk' => $this->customer_id,
            // ID unik string (CUST26xxx) dari tabel pelanggan
            'customer_uid' => $this->customer->customer_id ?? '-',
            'customer_name' => $this->customer->name ?? 'N/A',
            'service_name' => $this->service->name ?? 'N/A',
            'price_formatted' => 'Rp ' . number_format($this->service->price ?? 0, 0, ',', '.'),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ];
    }
}
