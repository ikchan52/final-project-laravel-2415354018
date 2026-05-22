<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'customer_id',
        'service_id',
        'start_date',
        'end_date',
        'status'
    ];

    /**
     * Relasi ke Customer. [cite: 506-512]
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi ke Service. [cite: 501-505]
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
