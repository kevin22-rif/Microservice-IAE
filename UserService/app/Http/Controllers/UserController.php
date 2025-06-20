<?php

namespace App\Http\Controllers;

use App\Models\User; // Pastikan model User diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Untuk meng-hash password

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Mengambil semua data pengguna dari database
        $users = User::all();

        // Mengembalikan respons JSON dengan data pengguna
        return response()->json($users);
    }

    /**
     * Menampilkan detail satu pengguna berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Mencari pengguna berdasarkan ID, atau gagal (404) jika tidak ditemukan
        $user = User::findOrFail($id);

        // Mengembalikan respons JSON dengan detail pengguna
        return response()->json($user);
    }

    /**
     * Menyimpan pengguna baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Melakukan validasi data yang masuk dari request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Email harus unik
            'password' => 'required|string|min:8', // Password minimal 8 karakter
        ]);

        // Meng-hash password sebelum disimpan
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Membuat pengguna baru di database menggunakan data yang sudah divalidasi
        $user = User::create($validatedData);

        // Mengembalikan respons JSON dengan data pengguna yang baru dibuat dan status 201 (Created)
        return response()->json($user, 201);
    }

    /**
     * Memperbarui data pengguna yang sudah ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Mencari pengguna berdasarkan ID, atau gagal (404) jika tidak ditemukan
        $user = User::findOrFail($id);

        // Melakukan validasi data yang masuk untuk update
        // 'email' harus unik, tapi abaikan email pengguna saat ini
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255', // 'sometimes' berarti opsional
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        // Jika password disertakan dalam request, hash password tersebut
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        // Memperbarui data pengguna
        $user->update($validatedData);

        // Mengembalikan respons JSON dengan data pengguna yang diperbarui
        return response()->json($user);
    }

    /**
     * Menghapus pengguna dari database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Mencari pengguna berdasarkan ID, atau gagal (404) jika tidak ditemukan
        $user = User::findOrFail($id);

        // Menghapus pengguna dari database
        $user->delete();

        // Mengembalikan respons kosong dengan status 204 (No Content)
        return response()->json(null, 204);
    }
}
