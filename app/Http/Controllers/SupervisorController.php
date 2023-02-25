<?php

namespace App\Http\Controllers;

use App\Events\CreatingBlockSupervisorEvent;
use App\Models\Block;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class SupervisorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $supervisors = Supervisor::paginate();

        return response()->view('backend.supervisors.index', [
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return response()->view('backend.supervisors.store');
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
            'phone' => 'required|string|min:7|max:13|unique:supervisors,phone',
            'email' => 'required|email|unique:supervisors,email',
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:supervisors,identity_no',
            'password' => ['required', Password::min(8)->uncompromised()->letters()->numbers(), 'string', 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $supervisor = new Supervisor();
            $supervisor->fname = $request->post('fname');
            $supervisor->sname = $request->post('sname');
            $supervisor->tname = $request->post('tname');
            $supervisor->lname = $request->post('lname');
            $supervisor->phone = $request->post('phone');
            $supervisor->identity_no = $request->post('identity_no');
            $supervisor->email = $request->post('email');
            $supervisor->password = Hash::make($request->post('password'));
            $supervisor->gender = $request->post('gender');
            $supervisor->status = $request->post('status');
            $supervisor->local_region = $request->post('local_region') ?? null;
            $supervisor->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/supervisors', 'public');
            }
            $supervisor->image = $image_path;
            $isCreated = $supervisor->save();

            event(new CreatingBlockSupervisorEvent($request, $supervisor));

            return response()->json([
                'message' => $isCreated
                    ? 'Supervisor added successfully.'
                    : 'Failed to add supervisor, please try again!'
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
     * @param  \App\Models\Supervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supervisor = Supervisor::findOrFail(Crypt::decrypt($id));
        //
        $last_block = Block::where([
            ['blocked_id', '=', $supervisor->id],
            ['position', '=', Supervisor::POSITION],
        ])->orderBy('created_at', 'DESC')->first();

        return response()->json([
            'supervisor' => $supervisor,
            'last_block' => $last_block ?? null,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supervisor = Supervisor::findOrFail(Crypt::decrypt($id));
        //
        return response()->view('backend.supervisors.update', [
            'supervisor' => $supervisor
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $supervisor = Supervisor::findOrFail(Crypt::decrypt($id));
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
            'phone' => 'required|string|min:7|max:13|unique:supervisors,phone,' . $supervisor->id,
            'email' => 'required|email|unique:supervisors,email,' . $supervisor->id,
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:supervisors,identity_no,' . $supervisor->id,
            'password' => ['nullable', Password::min(8)->uncompromised()->letters()->numbers(), 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $supervisor->fname = $request->post('fname');
            $supervisor->sname = $request->post('sname');
            $supervisor->tname = $request->post('tname');
            $supervisor->lname = $request->post('lname');
            $supervisor->phone = $request->post('phone');
            $supervisor->identity_no = $request->post('identity_no');
            $supervisor->email = $request->post('email');
            if ($request->post('password')) {
                $supervisor->password = Hash::make($request->post('password'));
            }
            $supervisor->gender = $request->post('gender');
            $supervisor->status = $request->post('status');
            $supervisor->local_region = $request->post('local_region') ?? null;
            $supervisor->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/supervisors', 'public');
                $supervisor->image = $image_path;
            }
            $isUpdated = $supervisor->save();

            return response()->json([
                'message' => $isUpdated
                    ? 'Supervisor updated successfully.'
                    : 'Failed to update supervisor, please try again!'
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
     * @param  \App\Models\Supervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supervisor = Supervisor::findOrFail(Crypt::decrypt($id));
        //
        if ($supervisor->delete()) {
            return response()->json([
                'title' => 'Deleted',
                'text' => 'Supervisor deleted successfully.',
                'icon' => 'success',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'title' => 'Failed!',
                'text' => 'Failed to delete supervisor, please try again!',
                'icon' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    // Get supervisors report
    public function getReport()
    {
        return Excel::download(new Supervisor(), 'supervisors.xlsx');
    }

    // Get supervisor report
    public function getReportSpecificSupervisor($id)
    {
        return Excel::download(new Supervisor(Crypt::decrypt($id)), 'supervisor.xlsx');
    }
}
