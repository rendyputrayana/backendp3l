<?php

namespace App\Http\Controllers;

use App\Models\RequestDonasi;
use Illuminate\Http\Request;

class RequestDonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requestDonasi = RequestDonasi::with(['organisasi'])
            ->orderBy('id_request')
            ->get();
        return response()->json([
            'status' => true,
            'message' => 'List Request Donasi',
            'data' => $requestDonasi
        ]);
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
        $request->validate([
            'id_organisasi' => 'required|exists:organisasis,id_organisasi',
            'detail_request' => 'required|string',
        ]);

        $requestDonasi = RequestDonasi::create([
            'id_organisasi' => $request->id_organisasi,
            'detail_request' => $request->detail_request,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Request Donasi berhasil ditambahkan',
            'data' => $requestDonasi
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(RequestDonasi $requestDonasi)
    {
        $requestDonasi = RequestDonasi::find($requestDonasi->id_request);
        if ($requestDonasi) {
            return response()->json([
                'status' => true,
                'message' => 'Detail Request Donasi',
                'data' => $requestDonasi
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request Donasi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RequestDonasi $requestDonasi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequestDonasi $requestDonasi)
    {
        $request->validate([
            'id_organisasi' => 'required|exists:organisasis,id_organisasi',
            'detail_request' => 'required|string',
        ]);

        $requestDonasi->update([
            'id_organisasi' => $request->id_organisasi,
            'detail_request' => $request->detail_request,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Request Donasi berhasil diperbarui',
            'data' => $requestDonasi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestDonasi $requestDonasi)
    {
        $requestDonasi = RequestDonasi::find($requestDonasi->id_request);
        if ($requestDonasi) {
            $requestDonasi->delete();
            return response()->json([
                'status' => true,
                'message' => 'Request Donasi berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request Donasi tidak ditemukan'
            ], 404);
        }
    }

    public function search($keyword)
    {
        $requestDonasi = RequestDonasi::where('detail_request', 'LIKE', "%$keyword%")->get();
        if ($requestDonasi->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Request Donasi tidak ditemukan'
            ], 404);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'List Request Donasi',
                'data' => $requestDonasi
            ]);
        }
    }

    public function filterByOrganisasi($id_organisasi)
    {
        $data = RequestDonasi::where('id_organisasi', $id_organisasi)->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Belum ada request donasi',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}