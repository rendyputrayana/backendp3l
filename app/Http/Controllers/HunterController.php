<?php

namespace App\Http\Controllers;

use App\Models\Hunter;
use Illuminate\Http\Request;

class HunterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hunter = Hunter::all();
        if(!$hunter){
            return response()->json([
                'message'=>'Hunter not found',
                'status'=> false,
                
            ]);
        }
        return response()->json([
            'message'=>'List Hunter',
            'status'=> true,
            'data'=>$hunter
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Hunter $hunter)
    {
        $hunter = Hunter::find($hunter->id_hunter);
        if(!$hunter)
        {
            return response()->json([
                'message'=>'Hunter not found',
                'status'=> false,
                
            ]);
        }else{
            return response()->json([
                'message'=>'Detail Hunter',
                'status'=> true,
                'data'=>$hunter
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hunter $hunter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hunter $hunter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hunter $hunter)
    {
        //
    }
}
