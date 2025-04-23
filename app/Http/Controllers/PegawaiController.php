<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawaIS = Pegawai::all();
        return response()->json([
            'status' => true,
            'message' => 'List Pegawai',
            'data' => $pegawaIS
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
        $pegawai = Pegawai::find($pegawai->id_pegawai);
        if ($pegawai) {
            return response()->json([
                'status' => true,
                'message' => 'Detail Pegawai',
                'data' => $pegawai
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
        ]);

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
