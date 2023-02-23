<?php

namespace App\Http\Controllers;

use App\Events\CreatingBlockAdminEvent;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $admins = Admin::paginate();

        return response()->view('backend.admins.index', [
            'admins' => $admins,
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
        return response()->view('backend.admins.store');
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
            'phone' => 'required|string|min:7|max:13|unique:admins,phone',
            'email' => 'required|email|unique:admins,email',
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:admins,identity_no',
            'password' => ['required', Password::min(8)->uncompromised()->letters()->numbers(), 'string', 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $admin = new Admin();
            $admin->fname = $request->post('fname');
            $admin->sname = $request->post('sname');
            $admin->tname = $request->post('tname');
            $admin->lname = $request->post('lname');
            $admin->phone = $request->post('phone');
            $admin->identity_no = $request->post('identity_no');
            $admin->email = $request->post('email');
            $admin->password = Hash::make($request->post('password'));
            $admin->gender = $request->post('gender');
            $admin->status = $request->post('status');
            $admin->local_region = $request->post('local_region') ?? null;
            $admin->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/admins', 'public');
            }
            $admin->image = $image_path;
            $isCreated = $admin->save();

            event(new CreatingBlockAdminEvent($request, $admin));

            return response()->json([
                'message' => $isCreated
                    ? 'Admin added successfully.'
                    : 'Failed to add admin, please try again!'
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
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = Admin::findOrFail(Crypt::decrypt($id));
        //
        return response()->view('backend.admins.update', [
            'admin' => $admin
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail(Crypt::decrypt($id));
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
            'phone' => 'required|string|min:7|max:13|unique:admins,phone,' . $admin->id,
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:admins,identity_no,' . $admin->id,
            'password' => ['nullable', Password::min(8)->uncompromised()->letters()->numbers(), 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $admin->fname = $request->post('fname');
            $admin->sname = $request->post('sname');
            $admin->tname = $request->post('tname');
            $admin->lname = $request->post('lname');
            $admin->phone = $request->post('phone');
            $admin->identity_no = $request->post('identity_no');
            $admin->email = $request->post('email');
            if ($request->post('password')) {
                $admin->password = Hash::make($request->post('password'));
            }
            $admin->gender = $request->post('gender');
            $admin->status = $request->post('status');
            $admin->local_region = $request->post('local_region') ?? null;
            $admin->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/admins', 'public');
                $admin->image = $image_path;
            }
            $isUpdated = $admin->save();

            return response()->json([
                'message' => $isUpdated
                    ? 'Admin updated successfully.'
                    : 'Failed to update admin, please try again!'
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
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail(Crypt::decrypt($id));
        //
        if ($admin->delete()) {
            return response()->json([
                'title' => 'Deleted',
                'text' => 'Admin deleted successfully.',
                'icon' => 'success',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'title' => 'Failed!',
                'text' => 'Failed to delete admin, please try again!',
                'icon' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    // Get admins report
    public function getReport()
    {
        return Excel::download(new Admin(), 'admins.xlsx');
    }

    // Get admin report
    public function getReportSpecificAdmin($id)
    {
        return Excel::download(new Admin(Crypt::decrypt($id)), 'admin.xlsx');
    }
}
