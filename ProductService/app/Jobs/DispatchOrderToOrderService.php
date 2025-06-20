<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http; // Untuk mengirim HTTP request ke OrderService

class DispatchOrderToOrderService implements ShouldQueue // Job ini dikirim ke RabbitMQ
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderData;

    public function __construct(array $orderData)
    {
        $this->orderData = $orderData;
    }

    public function handle()
    {
        // Logika mengirim data ke OrderService
        // PENTING: Di sini Anda panggil OrderService melalui endpoint API-nya
        try {
            $response = Http::timeout(30)->post('http://order-service:8000/api/orders', $this->orderData);

            if ($response->successful()) {
                \Log::info('Order successfully sent to OrderService.', ['order_data' => $this->orderData]);
            } else {
                \Log::error('Failed to send order to OrderService.', [
                    'order_data' => $this->orderData,
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error connecting to OrderService: ' . $e->getMessage(), ['order_data' => $this->orderData]);
        }
    }
}
