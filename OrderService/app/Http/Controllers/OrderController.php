<?php

namespace App\Http\Controllers;

use App\Models\Order; // Pastikan model Order diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Hanya jika Anda berencana menggunakan DB Facade langsung, kalau tidak bisa dihapus
use Illuminate\Support\Facades\Log; // Import Log Facade

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $orders = Order::all(); // Menggunakan Eloquent Model
        return response()->json($orders);
    }

    /**
     * Menampilkan detail satu pesanan berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    /**
     * Menyimpan pesanan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer', // <--- TAMBAHKAN INI!
            'quantity' => 'required|integer|min:1', // <--- TAMBAHKAN INI!
            'total_amount' => 'required|numeric|min:0',
            'status' => 'string|in:pending,completed,shipped,cancelled', // status tidak lagi 'required' karena sudah ada default di ProductController
        ]);

        // Karena status di ProductController bisa default 'pending' jika tidak dikirim,
        // kita juga bisa menjadikannya opsional di sini atau pastikan selalu ada di request dari ProductController.
        // Jika status bersifat opsional dari ProductService, bisa seperti ini:
        $validatedData['status'] = $validatedData['status'] ?? 'pending';


        try {
            // Menggunakan Eloquent Model untuk membuat order
            $order = Order::create($validatedData);

            Log::info('Order successfully created in OrderService database.', ['order_id' => $order->id, 'order_data' => $validatedData]);

            return response()->json([
                'message' => 'Order created successfully by OrderService.',
                'order_id' => $order->id
            ], 201); // 201 Created
        } catch (\Exception $e) {
            Log::error('Failed to create order in OrderService database: ' . $e->getMessage(), ['order_data' => $validatedData, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Error creating order in OrderService.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui data pesanan yang sudah ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'user_id' => 'sometimes|integer',
            'product_id' => 'sometimes|integer', // <--- TAMBAHKAN INI!
            'quantity' => 'sometimes|integer|min:1', // <--- TAMBAHKAN INI!
            'total_amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:pending,completed,shipped,cancelled',
        ]);

        $order->update($validatedData);
        return response()->json($order);
    }

    /**
     * Menghapus pesanan dari database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }
}
