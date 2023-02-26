<?php

namespace App\Http\Controllers;

use App\Events\AuthenticatedDraftUserEvent;
use App\Listeners\AuthenticatedDraftUserListener;
use App\Models\Admin;
use App\Models\AuthenticatedGuards;
use App\Models\Block;
use App\Models\Keeper;
use App\Models\Manager;
use App\Models\Supervisor;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    //

    public function showLogin($guard = 'manager')
    {
        return response()->view('auth.login', [
            'guard' => $guard,
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator($request->only([
            'username',
            'password',
            'guard',
        ]), [
            'username' => 'required|string',
            'password' => 'required|string',
            'guard' => 'required|string|in:' . implode(",", AuthenticatedGuards::GUARDS),
        ], [
            'guard.in' => 'Wrong URL, please try again!',
        ]);
        //
        if (!$validator->fails()) {
            $key = is_numeric($request->post('username')) ? 'identity_no' : 'email';
            $credintials = [
                $key => $request->post('username'),
                'password' => $request->post('password')
            ];

            // Get User
            $user = $this->getUser($request->post('guard'), $credintials, $key);
            if (is_null($user)) {
                return response()->json([
                    'message' => 'Wrong credentials, please try again!',
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($user->status === 'blocked' && Hash::check($credintials['password'], $user->password)) {
                // Check user block function
                return $this->isBlocked($credintials, $key === 'email' ? true : false, $request->post('guard'), $user);
            }

            if ($user->deleted_at) {
                return $this->isSoftDeleted($user);
            }

            if (Auth::guard($request->post('guard'))->attempt($credintials, false)) {
                event(new AuthenticatedDraftUserEvent(Auth::guard($request->post('guard'))->user()));
                return response()->json([
                    'message' => 'Login successfully',
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Failed to login, please try again!',
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json([
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function logout(Request $request)
    {
        $guard = 'manager';

        if (auth('admin')->check()) {
            $guard = 'admin';
        } else if (auth('supervisor')->check()) {
            $guard = 'supervisor';
        }else if (auth('keeper')->check()) {
            $guard = 'keeper';
        }

        $request->session()->invalidate();
        auth($guard)->logout();
        return redirect()->route('login', $guard);
    }

    // Check Blocked Users
    public function isBlocked($credintials, $is_email = false, $guard = 'manager', $user)
    {
        // Check user authenticate credentials
        if (Hash::check($credintials['password'], $user->password)) {
            if ($user->status === 'blocked') {

                // Last block
                $last_block = $user->blocks;

                if (!empty($last_block)) {
                    return response()->json([
                        'message' => 'Your account has been blocked: ' . $last_block[0]->description,
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
        }
    }

    // Deny SoftDeleted
    public function isSoftDeleted($user)
    {
        if ($user->deleted_at) {
            return response()->json([
                'message' => 'You cannot access to your account at this moment!',
            ], Response::HTTP_BAD_REQUEST);
        }
        return;
    }

    // Get user
    public function getUser($guard, $credintials, $key = 'identity_no')
    {
        $user = '';
        if ($guard === 'manager') {
            if ($key === 'email') {
                $user = Manager::where([
                    ['email', '=', $credintials['email']],
                ])->first();
            } else {
                $user = Manager::where([
                    ['identity_no', '=', $credintials['identity_no']],
                ])->first();
            }
        } else if ($guard === 'admin') {
            if ($key === 'email') {
                $user = Admin::where([
                    ['email', '=', $credintials['email']],
                ])->first();
            } else {
                $user = Admin::where([
                    ['identity_no', '=', $credintials['identity_no']],
                ])->first();
            }
        } else if ($guard === 'supervisor') {
            if ($key === 'email') {
                $user = Supervisor::where([
                    ['email', '=', $credintials['email']],
                ])->first();
            } else {
                $user = Supervisor::where([
                    ['identity_no', '=', $credintials['identity_no']],
                ])->first();
            }
        } else if ($guard === 'keeper') {
            if ($key === 'email') {
                $user = Keeper::where([
                    ['email', '=', $credintials['email']],
                ])->first();
            } else {
                $user = Keeper::where([
                    ['identity_no', '=', $credintials['identity_no']],
                ])->first();
            }
        }

        return $user;
    }
}
