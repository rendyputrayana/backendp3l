<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawaIS = Pegawai::with('pengguna')
            ->orderBy('id_pegawai') // tambahkan ini
            ->get();

        $pegawaiData = $pegawaIS->map(function($item) {
            return [
                'id_pegawai' => $item->id_pegawai,
                'nama_pegawai' => $item->nama_pegawai,
                'tanggal_lahir' => $item->tanggal_lahir,
                'email_pegawai' => $item->pengguna ? $item->pengguna->email : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'List Pegawai',
            'data' => $pegawaiData
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pegawai $pegawai)
    {
        $pegawai = Pegawai::with('pengguna')->findOrFail($pegawai->id_pegawai);
        $email_pegawai = $pegawai->pengguna ? $pegawai->pengguna->email : null;
        $pegawaiData = [
            'id_pegawai' => $pegawai->id_pegawai,
            'nama_pegawai' => $pegawai->nama_pegawai,
            'tanggal_lahir' => $pegawai->tanggal_lahir,
            'email_pegawai' => $email_pegawai,
        ];

        if ($pegawai) {
            return response()->json([
                'status' => true,
                'message' => 'Detail Pegawai',
                'data' => $pegawaiData
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai not found'
            ], 404);
        }
    }

    public function getPegawaiKurir($id_pegawai)
    {
        $kurir = Pegawai::where('id_pegawai', $id_pegawai)
            ->whereHas('jabatan', function ($query) {
                $query->where('nama_jabatan', 'Kurir');
            })
            ->first();

        if ($kurir) {
            return response()->json([
                'status' => true,
                'message' => 'Pegawai adalah Kurir',
                'data' => $kurir
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai bukan Kurir',
                'data' => null
            ], 404);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nama_pegawai' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'email_pegawai' => 'required|email',
        ]);

        $pengguna = $pegawai->pengguna;
        if ($pengguna) {
            $pengguna->update([
                'email' => $request->email_pegawai,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna not found'
            ], 404);
        }

        $pegawai->update([
            'nama_pegawai' => $request->nama_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pegawai updated successfully',
            'data' => $pegawai
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        $pegawai = Pegawai::find($pegawai->id_pegawai);
        if (!$pegawai) {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai not found'
            ], 404);
        }

        $pegawai->delete();
        return response()->json([
            'status' => true,
            'message' => 'Pegawai deleted successfully'
        ], 200);
    }

    public function search($keyword)
    {
        $pegawaIS = Pegawai::where('nama_pegawai', 'like', "%$keyword%")
                             ->orWhere('tanggal_lahir', 'like', "%$keyword%")
                             ->get();
    
        if ($pegawaIS->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Search results',
                'data' => $pegawaIS
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No results found'
            ], 404);
        }        
    }
    
}
