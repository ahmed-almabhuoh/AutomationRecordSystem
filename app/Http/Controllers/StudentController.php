<?php

namespace App\Http\Controllers;

use App\Events\CreatingBlockStudentEvent;
use App\Models\Block;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $students = Student::paginate();

        return response()->view('backend.students.index', [
            'students' => $students,
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
        return response()->view('backend.students.store');
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
            'phone' => 'required|string|min:7|max:13|unique:students,phone',
            'email' => 'required|email|unique:students,email',
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:students,identity_no',
            'password' => ['required', Password::min(8)->uncompromised()->letters()->numbers(), 'string', 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $student = new Student();
            $student->fname = $request->post('fname');
            $student->sname = $request->post('sname');
            $student->tname = $request->post('tname');
            $student->lname = $request->post('lname');
            $student->phone = $request->post('phone');
            $student->identity_no = $request->post('identity_no');
            $student->email = $request->post('email');
            $student->password = Hash::make($request->post('password'));
            $student->gender = $request->post('gender');
            $student->status = $request->post('status');
            $student->local_region = $request->post('local_region') ?? null;
            $student->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/students', 'public');
            }
            $student->image = $image_path;
            $isCreated = $student->save();

            event(new CreatingBlockStudentEvent($request, $student));

            return response()->json([
                'message' => $isCreated
                    ? 'Student added successfully.'
                    : 'Failed to add student, please try again!'
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
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = Student::findOrFail(Crypt::decrypt($id));
        $last_block = Block::where([
            ['blocked_id', '=', $student->id],
            ['position', '=', Student::POSITION],
        ])->orderBy('created_at', 'DESC')->first();
        //
        return response()->json([
            'student' => $student,
            'last_block' => $last_block ?? null,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::findOrFail(Crypt::decrypt($id));
        //
        return response()->view('backend.students.update', [
            'student' => $student
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail(Crypt::decrypt($id));
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
            'phone' => 'required|string|min:7|max:13|unique:students,phone,' . $student->id,
            'email' => 'required|email|unique:students,email,' . $student->id,
            'gender' => 'required|string|in:male,female',
            'status' => 'required|string|in:active,draft,blocked',
            'identity_no' => 'required|string|min:9|max:9|unique:students,identity_no,' . $student->id,
            'password' => ['nullable', Password::min(8)->uncompromised()->letters()->numbers(), 'max:25'],
            'image' => 'nullable',
            'local_region' => 'nullable|min:5|max:50',
            'description' => 'nullable|min:10|max:150',
        ]);
        //
        if (!$validator->fails()) {
            $student->fname = $request->post('fname');
            $student->sname = $request->post('sname');
            $student->tname = $request->post('tname');
            $student->lname = $request->post('lname');
            $student->phone = $request->post('phone');
            $student->identity_no = $request->post('identity_no');
            $student->email = $request->post('email');
            if ($request->post('password')) {
                $student->password = Hash::make($request->post('password'));
            }
            $student->gender = $request->post('gender');
            $student->status = $request->post('status');
            $student->local_region = $request->post('local_region') ?? null;
            $student->description = $request->post('description') ?? null;
            $image_path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image_path = $file->store('user/students', 'public');
                $student->image = $image_path;
            }
            $isUpdated = $student->save();

            return response()->json([
                'message' => $isUpdated
                    ? 'Student updated successfully.'
                    : 'Failed to update student, please try again!'
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
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Student::findOrFail(Crypt::decrypt($id));
        //
        if ($student->delete()) {
            return response()->json([
                'title' => 'Deleted',
                'text' => 'Student deleted successfully.',
                'icon' => 'success',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'title' => 'Failed!',
                'text' => 'Failed to delete student, please try again!',
                'icon' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    // Get students report
    public function getReport()
    {
        return Excel::download(new Student(), 'students.xlsx');
    }

    // Get student report
    public function getReportSpecificStudent($id)
    {
        return Excel::download(new Student(Crypt::decrypt($id)), 'student.xlsx');
    }
}
