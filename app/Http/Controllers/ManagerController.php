<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManagerRequest;
use App\Models\Manager;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $managers = Manager::paginate();

        return response()->view('backend.managers.index', [
            'managers' => $managers,
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
        return response()->view('backend.managers.store');
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
            'phone' => 'required|string|min:7|max:13|unique:managers,phone',
            'email' => 'required|email|unique:managers,email',
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:managers,identity_no',
            'password' => ['required', Password::min(8)->uncompromised()->letters()->numbers(), 'string', 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        // 
        if (!$validator->fails()) {
            $manager = new Manager();
            $manager->fname = $request->post('fname');
            $manager->sname = $request->post('sname');
            $manager->tname = $request->post('tname');
            $manager->lname = $request->post('lname');
            $manager->phone = $request->post('phone');
            $manager->identity_no = $request->post('identity_no');
            $manager->email = $request->post('email');
            $manager->password = Hash::make($request->post('password'));
            $manager->gender = $request->post('gender');
            $manager->status = $request->post('status');
            $manager->local_region = $request->post('local_region') ?? null;
            $manager->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/managers', 'public');
            }
            $manager->image = $image_path;
            $isCreated = $manager->save();

            return response()->json([
                'message' => $isCreated
                    ? 'Manager added successfully.'
                    : 'Failed to add manager, please try again!'
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
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function show(Manager $manager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $manager = Manager::findOrFail(Crypt::decrypt($id));
        //
        return response()->view('backend.managers.update', [
            'manager' => $manager
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $manager = Manager::findOrFail(Crypt::decrypt($id));
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
            'phone' => 'required|string|min:7|max:13|unique:managers,phone,' . $manager->id,
            'email' => 'required|email|unique:managers,email,' . $manager->id,
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:managers,identity_no,' . $manager->id,
            'password' => ['nullable', Password::min(8)->uncompromised()->letters()->numbers(), 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        // 
        if (!$validator->fails()) {
            $manager->fname = $request->post('fname');
            $manager->sname = $request->post('sname');
            $manager->tname = $request->post('tname');
            $manager->lname = $request->post('lname');
            $manager->phone = $request->post('phone');
            $manager->identity_no = $request->post('identity_no');
            $manager->email = $request->post('email');
            if ($request->post('password')) {
                $manager->password = Hash::make($request->post('password'));
            }
            $manager->gender = $request->post('gender');
            $manager->status = $request->post('status');
            $manager->local_region = $request->post('local_region') ?? null;
            $manager->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/managers', 'public');
                $manager->image = $image_path;
            }
            $isUpdated = $manager->save();

            return response()->json([
                'message' => $isUpdated
                    ? 'Manager updated successfully.'
                    : 'Failed to update manager, please try again!'
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
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $manager = Manager::findOrFail(Crypt::decrypt($id));
        //
        if ($manager->delete()) {
            return response()->json([
                'title' => 'Deleted',
                'text' => 'Manager deleted successfully.',
                'icon' => 'success',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'title' => 'Failed!',
                'text' => 'Failed to delete manager, please try again!',
                'icon' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    // Get managers report
    public function getReport()
    {
        return Excel::download(new Manager(), 'managers.xlsx');
    }

    // Get manager report
    public function getReportSpecificManager($id)
    {
        // $manager = Manager::findOrFail(Crypt::decrypt($id));
        // $manager = Manager::find(Crypt::decrypt($id));
        return Excel::download(new Manager(Crypt::decrypt($id)), 'manager.xlsx');
    }
}
