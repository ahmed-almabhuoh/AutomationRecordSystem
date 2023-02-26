<?php

namespace App\Http\Controllers;

use App\Events\CreatingBlockKeeperEvent;
use App\Models\Block;
use App\Models\Keeper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class KeeperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $keepers = Keeper::paginate();

        return response()->view('backend.keepers.index', [
            'keepers' => $keepers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('backend.keepers.store');
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
            'fname',
            'sname',
            'tname',
            'lname',
            'phone',
            'email',
            'gender',
            'status',
            'identity_no',
            'password',
            'image',
            'local_region',
            'description'
        ]), [
            'fname' => 'required|string|min:2|max:20',
            'sname' => 'required|string|min:2|max:20',
            'tname' => 'required|string|min:2|max:20',
            'lname' => 'required|string|min:2|max:20',
            'phone' => 'required|string|min:7|max:13|unique:keepers,phone',
            'email' => 'required|email|unique:keepers,email',
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:keepers,identity_no',
            'password' => ['required', Password::min(8)->uncompromised()->letters()->numbers(), 'string', 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $keeper = new Keeper();
            $keeper->fname = $request->post('fname');
            $keeper->sname = $request->post('sname');
            $keeper->tname = $request->post('tname');
            $keeper->lname = $request->post('lname');
            $keeper->phone = $request->post('phone');
            $keeper->identity_no = $request->post('identity_no');
            $keeper->email = $request->post('email');
            $keeper->password = Hash::make($request->post('password'));
            $keeper->gender = $request->post('gender');
            $keeper->status = $request->post('status');
            $keeper->local_region = $request->post('local_region') ?? null;
            $keeper->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/keepers', 'public');
            }
            $keeper->image = $image_path;
            $isCreated = $keeper->save();

            event(new CreatingBlockKeeperEvent($request, $keeper));

            return response()->json([
                'message' => $isCreated
                    ? 'Keeper added successfully.'
                    : 'Failed to add keeper, please try again!'
            ], $isCreated
                ? Response::HTTP_CREATED
                : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Keeper  $keeper
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $keeper = Keeper::findOrFail(Crypt::decrypt($id));
        $last_block = Block::where([
            ['blocked_id', '=', $keeper->id],
            ['position', '=', Keeper::POSITION],
        ])->orderBy('created_at', 'DESC')->first();
        //
        return response()->json([
            'keeper' => $keeper,
            'last_block' => $last_block ?? null,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Keeper  $keeper
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $keeper = Keeper::findOrFail(Crypt::decrypt($id));
        //
        return response()->view('backend.keepers.update', [
            'keeper' => $keeper
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Keeper  $keeper
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $keeper = Keeper::findOrFail(Crypt::decrypt($id));
        $validator = Validator($request->only([
            'fname',
            'sname',
            'tname',
            'lname',
            'phone',
            'email',
            'gender',
            'status',
            'identity_no',
            'password',
            'image',
            'local_region',
            'description'
        ]), [
            'fname' => 'required|string|min:2|max:20',
            'sname' => 'required|string|min:2|max:20',
            'tname' => 'required|string|min:2|max:20',
            'lname' => 'required|string|min:2|max:20',
            'phone' => 'required|string|min:7|max:13|unique:keepers,phone,' . $keeper->id,
            'email' => 'required|email|unique:keepers,email,' . $keeper->id,
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:keepers,identity_no,' . $keeper->id,
            'password' => ['nullable', Password::min(8)->uncompromised()->letters()->numbers(), 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $keeper->fname = $request->post('fname');
            $keeper->sname = $request->post('sname');
            $keeper->tname = $request->post('tname');
            $keeper->lname = $request->post('lname');
            $keeper->phone = $request->post('phone');
            $keeper->identity_no = $request->post('identity_no');
            $keeper->email = $request->post('email');
            if ($request->post('password')) {
                $keeper->password = Hash::make($request->post('password'));
            }
            $keeper->gender = $request->post('gender');
            $keeper->status = $request->post('status');
            $keeper->local_region = $request->post('local_region') ?? null;
            $keeper->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/keepers', 'public');
                $keeper->image = $image_path;
            }
            $isUpdated = $keeper->save();

            return response()->json([
                'message' => $isUpdated
                    ? 'Keeper updated successfully.'
                    : 'Failed to update keeper, please try again!'
            ], $isUpdated
                ? Response::HTTP_OK
                : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Keeper  $keeper
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keeper = Keeper::findOrFail(Crypt::decrypt($id));
        //
        if ($keeper->delete()) {
            return response()->json([
                'title' => 'Deleted',
                'text' => 'Keeper deleted successfully.',
                'icon' => 'success',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'title' => 'Failed!',
                'text' => 'Failed to delete keeper, please try again!',
                'icon' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    // Get keepers report
    public function getReport()
    {
        return Excel::download(new Keeper(), 'keepers.xlsx');
    }

    // Get keeper report
    public function getReportSpecificKeeper($id)
    {
        return Excel::download(new Keeper(Crypt::decrypt($id)), 'keeper.xlsx');
    }
}
