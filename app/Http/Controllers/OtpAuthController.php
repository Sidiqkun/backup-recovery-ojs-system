<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\OtpMail;

class OtpAuthController extends Controller
{
    public function showLogin()
    {
        // If already logged in, redirect
        if (session('admin_logged_in') === true) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function processLogin(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $adminEmail = env('ADMIN_EMAIL');

        if (!$adminEmail || $request->email !== $adminEmail) {
            return back()->withErrors(['email' => 'Email yang dikirimkan salah atau tidak terdaftar sebagai Admin.']);
        }

        // Generate 6 digit OTP
        $otp = (string) random_int(100000, 999999);
        
        // Save to cache for 5 minutes
        Cache::put('admin_otp', $otp, now()->addMinutes(5));
        
        // Indicate OTP was sent to allow access to verify screen
        session(['otp_sent' => true]);

        // Send Email
        Mail::to($adminEmail)->send(new OtpMail($otp));

        return redirect()->route('otp.verify');
    }

    public function showVerify()
    {
        if (!session('otp_sent')) {
            return redirect()->route('login');
        }

        $email = env('ADMIN_EMAIL');
        return view('auth.verify-otp', ['email' => $email]);
    }

    public function processVerify(Request $request)
    {
        // Accept the input either as an array of singular digits (like InputOTP UI might pass) or a string
        $otpCode = is_array($request->otp) ? implode('', $request->otp) : $request->otp;
        
        if (strlen($otpCode) !== 6) {
            return back()->withErrors(['otp' => 'Format kode OTP tidak sesuai.']);
        }

        $cachedOtp = Cache::get('admin_otp');

        if (!$cachedOtp || $cachedOtp !== $otpCode) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        // OTP Valid! Log in by setting session variable
        session(['admin_logged_in' => true]);
        
        // Clear OTP state
        Cache::forget('admin_otp');
        session()->forget('otp_sent');
        session()->regenerate(); // Protect against session fixation

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
