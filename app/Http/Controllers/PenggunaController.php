<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use App\Models\Pembeli;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Organisasi;
use App\Models\Hunter;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pengguna = Pengguna::all();
        return response()->json([
            'status' => true,
            'message' => 'Data Pengguna',
            'data' => $pengguna
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Method ini biasanya digunakan untuk menampilkan form,
        // tapi kita tidak perlu implementasikan ini jika menggunakan API.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email|unique:penggunas',
            'username' => 'required|unique:penggunas',
            'password' => 'required|confirmed',
            'role' => 'required|string', // Menentukan role pengguna
        ]);

        // Menyimpan pengguna baru berdasarkan role yang diberikan
        $pengguna = new Pengguna();
        $pengguna->email = $request->email;
        $pengguna->username = $request->username;
        $pengguna->password = bcrypt($request->password);

        if ($request->role == 'pembeli') {
            // Logic untuk Pembeli
            $pengguna->id_pembeli = Pembeli::create([])->id_pembeli;
        } elseif ($request->role == 'pegawai') {
            // Logic untuk Pegawai
            $pengguna->id_pegawai = Pegawai::create(['id_jabatan' => $request->id_jabatan])->id_pegawai;
        } elseif ($request->role == 'penitip') {
            // Logic untuk Penitip
            $pengguna->id_penitip = Penitip::create([])->id_penitip;
        } elseif ($request->role == 'organisasi') {
            // Logic untuk Organisasi
            $pengguna->id_organisasi = Organisasi::create([])->id_organisasi;
        } elseif ($request->role == 'hunter') {
            // Logic untuk Hunter
            $pengguna->id_hunter = Hunter::create([])->id_hunter;
        }

        $pengguna->save();

        return response()->json([
            'status' => true,
            'message' => 'Pengguna berhasil dibuat',
            'data' => $pengguna
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail Pengguna',
            'data' => $pengguna
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengguna $pengguna)
    {
        // Biasanya untuk form edit, tapi tidak perlu implementasi API.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|unique:penggunas,email,' . $id,
            'username' => 'required|unique:penggunas,username,' . $id,
            'password' => 'nullable|confirmed',
        ]);

        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        $pengguna->email = $request->email;
        $pengguna->username = $request->username;

        if ($request->password) {
            $pengguna->password = bcrypt($request->password);
        }

        $pengguna->save();

        return response()->json([
            'status' => true,
            'message' => 'Pengguna berhasil diperbarui',
            'data' => $pengguna
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        // Hapus data terkait (Pembeli, Pegawai, Penitip, Organisasi, Hunter)
        if ($pengguna->id_pembeli) {
            Pembeli::find($pengguna->id_pembeli)->delete();
        } elseif ($pengguna->id_pegawai) {
            Pegawai::find($pengguna->id_pegawai)->delete();
        } elseif ($pengguna->id_penitip) {
            Penitip::find($pengguna->id_penitip)->delete();
        } elseif ($pengguna->id_organisasi) {
            Organisasi::find($pengguna->id_organisasi)->delete();
        } elseif ($pengguna->id_hunter) {
            Hunter::find($pengguna->id_hunter)->delete();
        }

        $pengguna->delete();

        return response()->json([
            'status' => true,
            'message' => 'Pengguna berhasil dihapus'
        ]);
    }
}
