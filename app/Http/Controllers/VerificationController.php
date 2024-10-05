<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid verification code or email.'], 400);
        }

        $user->email_verified_at = now();
        $user->verification_code = null; // Clear the verification code
        $user->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function resendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->email_verified_at) {
            return response()->json(['message' => 'Invalid request.'], 400);
        }

        // Generate a new verification code
        $verificationCode = rand(100000, 999999);
        $user->verification_code = $verificationCode;
        $user->save();

        // Resend verification code
        Mail::to($user->email)->send(new VerifyEmail($user, $verificationCode));

        return response()->json(['message' => 'Verification code resent successfully.']);
    }
}
