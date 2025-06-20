<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Tambahkan 'product_id' dan 'quantity' ke dalam $fillable
    protected $fillable = [
        'user_id',
        'product_id',   // <--- TAMBAHKAN INI!
        'quantity',     // <--- TAMBAHKAN INI!
        'total_amount',
        'status',
        // 'created_at' dan 'updated_at' akan otomatis diisi oleh Laravel
    ];

    // Jika Anda punya relasi lain, bisa ditambahkan di sini
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }
}
