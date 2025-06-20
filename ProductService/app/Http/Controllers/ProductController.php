<?php

namespace App\Http\Controllers;

use App\Models\Product; // Pastikan model Product diimport
use Illuminate\Http\Request;
use App\Jobs\DispatchOrderToOrderService; // Import Job Anda
use Illuminate\Support\Facades\Log; // Import Log Facade untuk logging

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Menampilkan detail satu produk berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Menyimpan produk baru ke database (ini untuk POST /api/products).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }

    /**
     * Memproses permintaan order (ini untuk POST /api/products/order).
     * Menerima data order dan mendispatch job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processOrder(Request $request)
    {
        // Validasi data untuk memproses order
        // Perhatikan: 'status' TIDAK ADA aturan 'default:pending'
        $validatedOrderData = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'string|in:pending,completed,cancelled', // <-- ATURAN VALIDASI SUDAH BENAR
        ]);

        // TANGANI NILAI DEFAULT UNTUK STATUS SETELAH VALIDASI
        // Jika 'status' tidak dikirimkan di request, setel ke 'pending'
        $validatedOrderData['status'] = $validatedOrderData['status'] ?? 'pending';

        // Kirim Job ke antrean RabbitMQ
        // Job ini akan berisi logika untuk memanggil API OrderService
        DispatchOrderToOrderService::dispatch($validatedOrderData);

        Log::info('Order request dispatched to queue.', ['order_data' => $validatedOrderData]);

        return response()->json([
            'message' => 'Order request received and being processed asynchronously.',
            'order_data_submitted' => $validatedOrderData
        ], 202); // Status 202 Accepted karena diproses di latar belakang
    }

    /**
     * Memperbarui data produk yang sudah ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product->update($validatedData);
        return response()->json($product);
    }

    /**
     * Menghapus produk dari database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
