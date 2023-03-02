<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\AdminCreateRequest;
use App\Http\Requests\api\AdminUpdateRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function test(Request $request)
    {
        return response()->json([
            'image' => $request->hasFile('image'),
            'name' => $request->input('name')
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $admins = Admin::get();

        return response()->json([
            'admins' => $admins,
            'count' => count($admins),
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminCreateRequest $request)
    {
        $admin = new Admin();
        $admin->fname = $request->input('fname');
        $admin->sname = $request->input('sname');
        $admin->tname = $request->input('tname');
        $admin->lname = $request->input('lname');
        $admin->phone = $request->input('phone');
        $admin->identity_no = $request->input('identity_no');
        $admin->email = $request->input('email');
        $admin->password = Hash::make($request->input('password'));
        $admin->gender = $request->input('gender');
        $admin->status = $request->input('status');
        $admin->local_region = $request->input('local_region') ?? null;
        $admin->description = $request->input('description') ?? null;
        $image_path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image_path = $file->store('user/admins', 'public');
        }
        $admin->image = $image_path;
        $isCreated = $admin->save();

        return response()->json([
            'message' => $isCreated ? 'Admin created successfully' : 'Failed to add the admin, please try again later!',
            'admin' => $admin,
        ], $isCreated ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admin = Admin::find($id);
        if (is_null($admin))
            return response()->json([
                'message' => 'Admin not found!',
            ], Response::HTTP_BAD_REQUEST);
        //
        return response()->json([
            'admin' => $admin,
            // 'blocks' => $admin->blocks,
        ], Response::HTTP_OK);
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
        $admin = Admin::find($id);
        if (is_null($admin))
            return response()->json([
                'message' => 'Admin not found!',
            ], Response::HTTP_BAD_REQUEST);

            $request->validate([  'fname' => 'required|string|min:2|max:20',
            'sname' => 'required|string|min:2|max:20',
            'tname' => 'required|string|min:2|max:20',
            'lname' => 'required|string|min:2|max:20',
            'phone' => 'required|string|min:7|max:13|unique:admins,phone,' . $admin->id,
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:admins,identity_no,' . $admin->id,
            'password' => ['nullable', Password::min(8)->uncompromised()->letters()->numbers(), 'max:25'],
            'image' => 'nullable|image',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',]);

        $admin->fname = $request->input('fname');
        $admin->sname = $request->input('sname');
        $admin->tname = $request->input('tname');
        $admin->lname = $request->input('lname');
        $admin->phone = $request->input('phone');
        $admin->identity_no = $request->input('identity_no');
        $admin->email = $request->input('email');
        if ($request->input('password')) {
            $admin->password = Hash::make($request->input('password'));
        }
        $admin->gender = $request->input('gender');
        $admin->status = $request->input('status');
        $admin->local_region = $request->input('local_region') ?? null;
        $admin->description = $request->input('description') ?? null;
        $image_path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image_path = $file->store('user/admins', 'public');
            $admin->image = $image_path;
        }
        $isUpdated = $admin->save();

        return response()->json([
            'message' => $isUpdated ? 'Admin updated successfully' : 'Failed to update the admin, please try again later!',
            'admin' => $admin
        ], $isUpdated ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $isDeleted = $admin->delete();
        //
        return response()->json([
            'message' => $isDeleted ? 'Admin deleted successfully' : 'Failed to delete the admin, please try again later!',
        ], $isDeleted ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    // Get admins report
    public function getReport()
    {
        return Excel::download(new Admin(), 'admins.xlsx');
    }

    // Get admin report
    public function getReportSpecificAdmin($id)
    {
        // return Excel::download(new Admin(Crypt::decrypt($id)), 'admin.xlsx');
        return Excel::download(new Admin($id), 'admin.xlsx');
    }
}
