<?php

namespace App\Http\Controllers\drug_information;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class NdcGpiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if($request->ndc){

            $data = DB::table('DRUG_MASTER')
            ->where('NDC', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('LABEL_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('GENERIC_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PACKAGE_SIZE', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        }

        if($request->gpi){

            $data = DB::table('DRUG_MASTER')
            ->where('NDC', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('LABEL_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('GENERIC_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PACKAGE_SIZE', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('GENERIC_PRODUCT_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        }

        

        return $this->respondWithToken($this->token(), '', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
