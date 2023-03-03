<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\SanctumAuthenticationRequest;
use App\Models\Admin;
use App\Models\Keeper;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    //

    public function checkNullUser ($user = null) {
        if (is_null($user)){
            return \response()->json([
                'message' => 'User not found!',
            ], Response::HTTP_BAD_REQUEST);
        }else {
            return;
        }
    }

//    Login
    public function login (SanctumAuthenticationRequest $request) {
        $user = null;
        $credentials = [
            'identity_no' => $request->post('username'),
            'password' => $request->post('password'),
        ];

//        Check User From Requested Guard
        if ($request->post('guard') === 'admin_api') {
            $user = Admin::where('identity_no', '=', $request->post('username'))->first();
            $this->checkNullUser($user);
        }else if ($request->post('guard') === 'supervisor_api') {
            $user = Supervisor::where('identity_no', '=', $request->post('username'))->first();
            $this->checkNullUser($user);
        }else if ($request->post('guard') === 'keeper_api') {
            $user = Keeper::where('identity_no', '=', $request->post('username'))->first();
            $this->checkNullUser($user);
        }else if ($request->post('guard') === 'parent_api') {
            $user = StudentParent::where('identity_no', '=', $request->post('username'))->first();
            $this->checkNullUser($user);
        }else if ($request->post('guard') === 'student_api') {
            $user = Student::where('identity_no', '=', $request->post('username'))->first();
            $this->checkNullUser($user);
        }

//        Create User Requested Token
        if (Auth::guard($request->post('guard'))->attempt($credentials, false)) {
            $token = $user->createToken('Memorization API Token')->plainTextToken;
            return \response()->json([
                'message' => 'Login successfully',
                '_token' => $token,
                'user' => $user,
            ], Response::HTTP_CREATED);
        }else {
            return \response()->json([
                'message' => 'Failed to login!',
            ], Response::HTTP_BAD_GATEWAY);
        }
    }
}
