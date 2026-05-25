<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    protected $fillable = ['customer_id', 'name', 'email', 'phone', 'address', 'status'];

    protected static function booted()
    {
        static::creating(function ($customer) {
            // 1. Ambil 2 digit tahun sekarang (misal: 26)
            $year = date('y');
            $prefix = 'CUST' . $year;

            // 2. Cari ID terakhir di tahun yang sama
            $lastCustomer = self::where('customer_id', 'LIKE', $prefix . '%')
                ->orderBy('customer_id', 'desc')
                ->first();

            if ($lastCustomer) {
                // Ambil 4 digit terakhir, tambah 1
                $lastSequence = substr($lastCustomer->customer_id, -4);
                $newSequence = str_pad((int)$lastSequence + 1, 4, '0', STR_PAD_LEFT);
            } else {
                // Kalau pelanggan pertama di tahun ini
                $newSequence = '0001';
            }

            // 3. Pasang ke property customer_id
            $customer->customer_id = $prefix . $newSequence;
        });
    }

    protected function customerIdVisual(): Attribute
    {
        return Attribute::make(
            get: fn () => 'CUST-' . str_pad($this->id, 3, '0', STR_PAD_LEFT),
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tambahkan di dalam class Customer
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'customer_id');
    }

    /**
     * Helper untuk mengambil langganan terbaru saja
     */
    public function latestSubscription()
    {
        return $this->hasOne(Subscription::class, 'customer_id')->latestOfMany();
    }
}
