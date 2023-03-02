<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Block;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlocksController extends Controller
{
    //
    // Return All Blocks Requests
    public function returnRepsonse($blocks = 'No blocks found!')
    {
        return response()->json([
            'blocks' => $blocks,
        ], Response::HTTP_OK);
    }

    // Filter All Blocks Results
    public function filterBlockResult($filters = [])
    {
        return Block::where($filters)->get();
    }


    // All Blocks
    public function index()
    {
        $blocks = Block::all();
        return $this->returnRepsonse($blocks);
    }

    // Admin Blocks
    public function admins()
    {
        // $blocks = Block::where([
        //     ['position', '=', Admin::POSITION]
        // ])->get();

        $blocks = $this->filterBlockResult([
            ['position', '=', Admin::POSITION]
        ]);

        return $this->returnRepsonse($blocks);
    }

    // Block Admin Between Duration
    public function blocksBetween(Request $request)
    {
        // 'datetime_field' => 'required|date_format:Y-m-d H:i:s|after:2000-01-01 00:00:00|before:2030-12-31 23:59:59',
        $request->validate([
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s',
        ]);
        $start = $request->post('start');
        $end = $request->post('end');

        $blocks = Block::where('position', '=', Admin::POSITION)
            ->where(function ($query) use ($start, $end) {
                // $query->whereBetween('from', [$start, $end])
                //     ->orWhereBetween('to', [$start, $end]);
                $query->where([
                    ['from', '<=', $start],
                    ['to', '>=', $end],
                ]);
            })
            ->get();

        return $this->returnRepsonse($blocks);
    }

    // Blocks Admin Status
    public function blockAdminStatus($status = 'active')
    {
        $blocks = $this->filterBlockResult([
            ['status', '=', $status],
            ['position', '=', Admin::POSITION],
        ]);

        // $blocks = Block::where([
        //     ['status', '=', $status],
        //     ['position', '=', Admin::POSITION],
        // ])->get();

        return $this->returnRepsonse($blocks);
    }

    // Blocks Admin Blocks
    public function getAdminBlocks($id)
    {
        $admin = Admin::find($id);
        if (is_null($admin))
            return response()->json([
                'message' => 'Admin not found!'
            ], Response::HTTP_BAD_REQUEST);

        return $this->returnRepsonse($admin->blocks);
    }

    // Blocks Admin Blocks With Status
    public function getAdminWithStatusBlocks($id, $status = 'active')
    {
        // scopeAdminDisabledBlocks
        // scopeAdminActiveBlocks
        $admin = Admin::find($id);
        if (is_null($admin))
            return response()->json([
                'message' => 'Admin not found!'
            ], Response::HTTP_BAD_REQUEST);

        $blocks = $status === 'active' ? $blocks = Block::adminActiveBlocks()->get() : $blocks = Block::adminDisabledBlocks()->get();;
        // if ($status == 'disabled') {
        //     $blocks = Block::adminActiveBlocks()->get();
        // } else {
        //     $blocks = Block::adminDisabledBlocks()->get();
        // }

        return $this->returnRepsonse($blocks);
    }

    // Search for admin blocks
    public function searchForAdminBlocks($searchTerm = '')
    {
        $blocks = Block::where('description', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere(function ($query) use ($searchTerm) {
                $query->where('from', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('to', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('created_at', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('updated_at', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('blocked_id', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('position', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('id', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('status', 'LIKE', '%' . $searchTerm . '%');
            })
            ->get();


        return $this->returnRepsonse($blocks);
    }
}
