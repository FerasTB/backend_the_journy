<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->validated();

        $identifier = $request->identifier;
        $user = null;

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
        } else {
            $user = User::where('phone', $identifier)->first();
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken("the_journy_app")->plainTextToken;
        return response()->json([
            'status' => 'alright',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $request->validated();
        $user = auth()->user();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'error' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->newPassword)
        ]);

        return response()->json([
            'status' => 'password changed',
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $fields = $request->validated();
        $fields['password'] = Hash::make($request->password);

        // Generate a 6-digit verification code
        $verificationCode = rand(100000, 999999);
        $fields['verification_code'] = $verificationCode;

        $user = User::create($fields);

        $token = $user->createToken("the_journy_app")->plainTextToken;

        $user->info()->create([
            'country' => 'Syria',
            'numberPrefix' => '+963',
        ]);

        // Send verification code to user's email
        Mail::to($user->email)->send(new VerifyEmail($user, $verificationCode));

        return response()->json([
            'status' => 'alright',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->noContent();
    }
}
