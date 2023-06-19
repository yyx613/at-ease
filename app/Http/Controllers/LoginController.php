<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index() {
        return view('pages.login');
    }

    public function submit(Request $request) {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter an email',
            'email.unique' => 'This email address has already been taken',
            'password.required' => 'Please enter a password',
        ]);

        if ($validator->fails()) {
            return redirect(route('login'))->withErrors($validator)->withInput();
        }

        // Auth login
        $inputs = $validator->validated();
        if (Auth::attempt(['email' => $inputs['email'], 'password' => $inputs['password'], 'role_id' => Role::ROLE_DRIVER_ID])) {
            return redirect(route('customer-list'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout() {
        session()->flush(); // Clear all sessions
        Auth::logout();

        return redirect(route('login'));
    }
}
