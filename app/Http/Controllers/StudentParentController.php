<?php

namespace App\Http\Controllers;

use App\Events\CreatingBlockStudentParentEvent;
use App\Models\Block;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class StudentParentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $student_parents = StudentParent::paginate();

        return response()->view('backend.student_parents.index', [
            'student_parents' => $student_parents,
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
        return response()->view('backend.student_parents.store');
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
            'phone' => 'required|string|min:7|max:13|unique:student_parents,phone',
            'email' => 'required|email|unique:student_parents,email',
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:student_parents,identity_no',
            'password' => ['required', Password::min(8)->uncompromised()->letters()->numbers(), 'string', 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $student_parent = new StudentParent();
            $student_parent->fname = $request->post('fname');
            $student_parent->sname = $request->post('sname');
            $student_parent->tname = $request->post('tname');
            $student_parent->lname = $request->post('lname');
            $student_parent->phone = $request->post('phone');
            $student_parent->identity_no = $request->post('identity_no');
            $student_parent->email = $request->post('email');
            $student_parent->password = Hash::make($request->post('password'));
            $student_parent->gender = $request->post('gender');
            $student_parent->status = $request->post('status');
            $student_parent->local_region = $request->post('local_region') ?? null;
            $student_parent->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/student_parents', 'public');
            }
            $student_parent->image = $image_path;
            $isCreated = $student_parent->save();

            event(new CreatingBlockStudentParentEvent($request, $student_parent));

            return response()->json([
                'message' => $isCreated
                    ? 'StudentParent added successfully.'
                    : 'Failed to add student_parent, please try again!'
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
     * @param  \App\Models\StudentParent  $studentParent
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student_parent = StudentParent::findOrFail(Crypt::decrypt($id));
        $last_block = Block::where([
            ['blocked_id', '=', $student_parent->id],
            ['position', '=', StudentParent::POSITION],
        ])->orderBy('created_at', 'DESC')->first();
        //
        return response()->json([
            'student_parent' => $student_parent,
            'last_block' => $last_block ?? null,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StudentParent  $studentParent
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student_parent = StudentParent::findOrFail(Crypt::decrypt($id));
        //
        return response()->view('backend.student_parents.update', [
            'student_parent' => $student_parent
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StudentParent  $studentParent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $student_parent = StudentParent::findOrFail(Crypt::decrypt($id));
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
            'phone' => 'required|string|min:7|max:13|unique:student_parents,phone,' . $student_parent->id,
            'email' => 'required|email|unique:student_parents,email,' . $student_parent->id,
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:student_parents,identity_no,' . $student_parent->id,
            'password' => ['nullable', Password::min(8)->uncompromised()->letters()->numbers(), 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $student_parent->fname = $request->post('fname');
            $student_parent->sname = $request->post('sname');
            $student_parent->tname = $request->post('tname');
            $student_parent->lname = $request->post('lname');
            $student_parent->phone = $request->post('phone');
            $student_parent->identity_no = $request->post('identity_no');
            $student_parent->email = $request->post('email');
            if ($request->post('password')) {
                $student_parent->password = Hash::make($request->post('password'));
            }
            $student_parent->gender = $request->post('gender');
            $student_parent->status = $request->post('status');
            $student_parent->local_region = $request->post('local_region') ?? null;
            $student_parent->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/student_parents', 'public');
                $student_parent->image = $image_path;
            }
            $isUpdated = $student_parent->save();

            return response()->json([
                'message' => $isUpdated
                    ? 'StudentParent updated successfully.'
                    : 'Failed to update student_parent, please try again!'
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
     * @param  \App\Models\StudentParent  $studentParent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student_parent = StudentParent::findOrFail(Crypt::decrypt($id));
        //
        if ($student_parent->delete()) {
            return response()->json([
                'title' => 'Deleted',
                'text' => 'Parent deleted successfully.',
                'icon' => 'success',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'title' => 'Failed!',
                'text' => 'Failed to delete parent, please try again!',
                'icon' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    // Get student parents report
    public function getReport()
    {
        return Excel::download(new StudentParent(), 'student_parents.xlsx');
    }

    // Get student parent report
    public function getReportSpecificStudentParent($id)
    {
        return Excel::download(new StudentParent(Crypt::decrypt($id)), 'student_parent.xlsx');
    }
}
