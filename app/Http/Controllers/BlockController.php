<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class BlockController extends Controller
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
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function show($blocked_id, $position = 'manager')
    {
        $blocked_id = Crypt::decrypt($blocked_id);
        $blocks = Block::where([
            ['position', '=', $position],
            ['blocked_id', '=', $blocked_id],
        ])->paginate();
        //
        return response()->view('backend.blocks.index', [
            'position' => $position,
            'blocks' => $blocks,
            'user' => $this->getUser($blocked_id, $position),
        ]);
    }

    public function getUser ($id, $position = 'manager') {
        if ($position === 'manager') {
            return Manager::findOrFail($id);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function edit(Block $block)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Block $block)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function destroy(Block $block)
    {
        //
    }
}
