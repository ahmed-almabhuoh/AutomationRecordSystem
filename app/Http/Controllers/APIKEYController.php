<?php

namespace App\Http\Controllers;

use App\Models\APIKEY;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class APIKEYController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return response()->view('backend.apis.store');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator($request->only([
            'uuid',
            'key',
            'secret',
            'name',
            'status',
            'rate_limit',
        ]), [
            'uuid' => 'required|string|unique:a_p_i_k_e_y_s,id',
            'key' => 'required|string|unique:a_p_i_k_e_y_s,key',
            'secret' => 'required|string',
            'name' => 'required|string|min:3|max:50',
            'status' => 'required|string|in:active,disabled',
            'rate_limit' => 'required|integer|min:0',
        ]);
        //
        if (!$validator->fails()) {
            $api = new APIKEY();
            $api->id = $request->input('uuid');
            $api->key = $request->input('key');
            $api->secret = Hash::make($request->input('secret'));
            $api->name = $request->input('name');
            $api->status = $request->input('status');
            $api->rat_limit = $request->input('rate_limit');
            $api->manager_id = auth('manager')->user()->id;
            $isCreated = $api->save();

            return response()->json([
                'message' => $isCreated ? 'API stored successfull.' : 'Failed to store the API, please try agian!',
            ], $isCreated ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\APIKEY  $aPIKEY
     * @return \Illuminate\Http\Response
     */
    public function show(APIKEY $aPIKEY)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\APIKEY  $aPIKEY
     * @return \Illuminate\Http\Response
     */
    public function edit(APIKEY $aPIKEY)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\APIKEY  $aPIKEY
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, APIKEY $aPIKEY)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\APIKEY  $aPIKEY
     * @return \Illuminate\Http\Response
     */
    public function destroy(APIKEY $aPIKEY)
    {
        //
    }
}
