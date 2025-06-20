<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// use App\Models\Order; // Jika Anda punya model Order

class ProcessIncomingOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderData;

    public function __construct(array $orderData)
    {
        $this->orderData = $orderData;
    }

    public function handle()
    {
        // Logika membuat order di database OrderService
        try {
            // Contoh: Menyimpan ke database OrderService
            // Order::create($this->orderData); // Jika menggunakan Eloquent Model
            \DB::table('orders')->insert([ // Jika menggunakan DB Facade
                'user_id' => $this->orderData['user_id'],
                'total_amount' => $this->orderData['total_amount'],
                'status' => $this->orderData['status'] ?? 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Log::info('Order processed and saved in OrderService database.', ['order_data' => $this->orderData]);

            // Bisa juga dispatch event lain dari sini, misal:
            // SendOrderConfirmationEmail::dispatch($this->orderData['user_id']);

        } catch (\Exception $e) {
            \Log::error('Failed to process incoming order: ' . $e->getMessage(), ['order_data' => $this->orderData]);
            // Logika retry atau dead-letter queue bisa ditambahkan di sini
        }
    }
}
