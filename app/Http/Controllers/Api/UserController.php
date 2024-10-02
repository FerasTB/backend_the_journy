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

    public function updatePersonalInfo(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the incoming data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            // 'country' => 'required|string|max:100',
            // 'city' => 'required|string|max:100',
            // 'website_url' => 'nullable|url|max:255',
            // 'linkedin_url' => 'nullable|url|max:255',
        ]);

        // Update user's personal information
        $user->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'country' => $validatedData['country'],
            'city' => $validatedData['city'],
            'website_url' => $validatedData['website_url'],
            'linkedin_url' => $validatedData['linkedin_url'],
        ]);

        return response()->json(['message' => 'Personal information updated successfully']);
    }
}
