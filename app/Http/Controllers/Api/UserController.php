<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LinkedinAnalysis;
use App\Models\SectionFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'website_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
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

    public function storeLinkedInAnalysis(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Start a transaction to ensure all operations are successful
        DB::beginTransaction();

        try {
            // Loop through each section
            $sections = ['Name', 'Headline', 'Summary', 'Experience', 'Education', 'Skills', 'Certifications', 'Languages'];

            foreach ($sections as $section) {
                if ($request->has($section)) {
                    $data = $request->input($section);

                    // Save section data
                    SectionFeedback::updateOrCreate(
                        ['user_id' => $user->id, 'section_name' => $section],
                        [
                            'original_section_text' => $data['original_section_text'],
                            'notes' => $data['notes'], // Store as JSON
                            'advice' => $data['advice'], // Store as JSON
                            'enhanced_section_text' => $data['enhanced_section_text'],
                            'score' => $data['score']
                        ]
                    );
                }
            }

            // Save the overall score
            if ($request->has('Overall Score')) {
                LinkedinAnalysis::updateOrCreate(
                    ['user_id' => $user->id],
                    ['overall_score' => $request->input('Overall Score')]
                );
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'LinkedIn analysis data stored successfully.'], 201);
        } catch (\Exception $e) {
            // Rollback the transaction in case of any failure
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to store LinkedIn analysis data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getLinkedInAnalysis(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Retrieve the overall analysis
            $overallAnalysis = LinkedInAnalysis::where('user_id', $user->id)->first();

            // Retrieve the section feedbacks
            $sections = SectionFeedback::where('user_id', $user->id)->get();

            // Prepare the response data
            $responseData = [];

            // Include each section's data
            foreach ($sections as $section) {
                $responseData[$section->section_name] = [
                    'original_section_text' => $section->original_section_text,
                    'notes' => $section->notes, // Will be automatically cast to array
                    'advice' => $section->advice, // Will be automatically cast to array
                    'enhanced_section_text' => $section->enhanced_section_text,
                    'score' => $section->score,
                ];
            }

            // Add the overall score
            if ($overallAnalysis) {
                $responseData['Overall Score'] = $overallAnalysis->overall_score;
            } else {
                $responseData['Overall Score'] = null;
            }

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve LinkedIn analysis data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
