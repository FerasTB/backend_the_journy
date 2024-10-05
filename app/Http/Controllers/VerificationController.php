<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verifyEmail($code)
    {
        $user = User::where('verification_code', $code)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid verification code.'], 400);
        }

        $user->email_verified_at = now();
        $user->verification_code = null; // Clear the code
        $user->save();

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }
}
