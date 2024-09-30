<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateLinkedinUrl(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'linkedin_url' => 'nullable|url',
        ]);

        // Get the authenticated user (or pass the user ID if necessary)
        $user = auth()->user();

        // Update the linkedin_url field
        $user->linkedin_url = $validatedData['linkedin_url'];
        $user->save();

        return response()->json([
            'message' => 'LinkedIn URL updated successfully',
            'user' => $user
        ], 200);
    }
}
