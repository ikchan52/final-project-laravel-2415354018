<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionGuard extends Command
{
    // Nama perintah yang akan dipanggil
    protected $signature = 'subscription:guard';

    // Deskripsi perintah
    protected $description = 'Memutus otomatis layanan yang sudah melewati end_date';

    public function handle()
    {
        $today = Carbon::now();

        // Cari langganan yang masih aktif/trial tapi sudah kadaluwarsa
        $expiredSubscriptions = Subscription::whereIn('status', ['active', 'trial'])
            ->where('end_date', '<', $today)
            ->get();

        $count = $expiredSubscriptions->count();

        foreach ($expiredSubscriptions as $sub) {
            // Kita ubah statusnya menjadi dismantle (sesuai standar Figma kamu)
            $sub->update(['status' => 'dismantle']);

            $this->info("Layanan untuk Customer ID {$sub->customer_id} telah DIHENTIKAN.");
        }

        $this->comment("Total {$count} layanan berhasil diterminasi hari ini.");
    }
}
