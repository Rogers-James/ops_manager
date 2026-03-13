<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function adminLoginPage()
    {
        return view('admin.pages.auth.login');
    }
    public function adminForgotPage()
    {
        return view('admin.pages.auth.forgot-password');
    }
    public function adminResetPage($token)
    {
        return view('admin.pages.auth.reset', compact('token'));
    }

    public function adminLoginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt login using WEB guard (default)
        if (!Auth::attempt($credentials)) {
            return back()->with('error', '❌ Record not matched with data !!!');
        }

        // Prevent session fixation
        $request->session()->regenerate();

        $user = Auth::user();

        // Allow only admins to login via /admin
        if (!$user->hasRole('admin')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('error', 'You don’t have access to the Admin Portal !!!');
        }

        // Optional: store active area
        session(['area' => 'admin']);

        return redirect()->route('admin.index.get')->with('success', 'Login as Admin Successfully !!!');
    }

    public function adminForgotPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not found in our records.');
        }

        // Optional: only admins can reset via admin portal
        if (!$user->hasRole('admin')) {
            return back()->with('error', 'This email is not allowed to reset from Admin portal.');
        }

        // Prevent spam: existing token in last 15 minutes
        $existingToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>=', Carbon::now()->subMinutes(15))
            ->first();

        if ($existingToken) {
            return back()->with('error', 'A password reset code has already been sent in the last 15 minutes.');
        }

        $token = mt_rand(100000, 999999);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => $token,
            'created_at' => Carbon::now(),
        ]);

        // TODO: send email (or SMS)
        // Mail::send('emails.admin-password', ['token' => $token], function ($message) use ($request) {
        //     $message->to($request->email)->subject('Reset Your Password');
        // });

        return back()->with('success', 'Password reset code sent! Please check your email.');
    }


    public function adminResetPost(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|min:8',
            'cpassword' => 'required|same:password',
            'token'     => 'required'
        ]);

        $resetRequest = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->first();

        if (!$resetRequest) {
            return back()->with('error', 'Invalid or expired password reset token.');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Account not found.');
        }

        // Optional: only admins can reset via admin portal
        if (!$user->hasRole('admin')) {
            return back()->with('error', 'This account is not allowed to reset from Admin portal.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('admin.login.get')->with('success', 'Password updated successfully!');
    }


    public function adminlogout(Request $request)
    {
        Auth::logout(); // web guard

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.get')->with('success', 'Logout Successfully !!!');
    }
}
