<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CV;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\Skill;
use App\Models\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CVController extends Controller
{
    public function index()
    {
        return CV::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string',
        ]);

        return CV::create([
            'user_id' => Auth::id(),
            'job_title' => $request->job_title,
        ]);
    }


    public function show($id)
    {
        $cv = CV::findOrFail($id);

        if ($cv->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($cv);
    }

    public function update(Request $request, $id)
    {
        $cv = CV::findOrFail($id);

        if ($cv->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'job_title' => 'required|string',
        ]);

        $cv->update($request->all());

        return response()->json($cv);
    }


    public function destroy($id)
    {
        $cv = CV::findOrFail($id);

        if ($cv->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $cv->delete();

        return response(null, 204);
    }


    public function get_CV()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $cvData = [
            'personal_info' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'city' => $user->city,
                'website_url' => $user->website_url,
                'linkedin_url' => $user->linkedin_url,
            ],
            'job_title' => $user->cv ? $user->cv->job_title : null,
            'summary' => $user->summary ? $user->summary->summary : null,
            'skills' => $user->skills ? $user->skills->pluck('skill_name') : [],
            'education' => $user->education ? $user->education->map(function ($edu) {
                return [
                    'university_name' => $edu->university_name,
                    'university_start_date' => $edu->university_start_date,
                    'university_end_date' => $edu->university_end_date,
                    'university_location' => $edu->university_location,
                    'specialization' => $edu->specialization,
                ];
            }) : [],
            'certificates' => $user->certificates ? $user->certificates->map(function ($cert) {
                return [
                    'certificate_name' => $cert->certificate_name,
                    'certificate_date' => $cert->certificate_date,
                ];
            }) : [],
            'experiences' => $user->experiences ? $user->experiences->map(function ($experience) {
                return [
                    'experience_name' => $experience->exper_name,
                    'company_name' => $experience->company_name,
                    'company_location' => $experience->company_location,
                    'start_date' => $experience->exper_start_date,
                    'end_date' => $experience->exper_end_date,
                ];
            }) : [],
            'languages' => $user->languages ? $user->languages->pluck('language_name') : [],
            'references' => $user->references ? $user->references->map(function ($ref) {
                return [
                    'first_name' => $ref->ref_first_name,
                    'last_name' => $ref->ref_last_name,
                    'phone' => $ref->ref_phone,
                ];
            }) : []
        ];

        return response()->json($cvData);
    }

    public function storeCV(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Start a transaction to ensure all operations are successful
        DB::beginTransaction();

        try {
            // Handle Professional Summary if provided
            if ($request->has('Professional Summary') && $request->input('Professional Summary') !== 'not mentioned') {
                Summary::updateOrCreate(
                    ['user_id' => $user->id],
                    ['summary' => $request->input('Professional Summary')]
                );
            }

            // Handle Experience if provided
            if ($request->has('Experience')) {
                foreach ($request->input('Experience') as $experienceData) {
                    if (isset($experienceData['title']) && $experienceData['title'] !== 'not mentioned') {
                        Experience::create([
                            'user_id' => $user->id,
                            'exper_name' => $experienceData['title'],
                            'company_name' => $experienceData['company'],
                            'exper_start_date' => $experienceData['start_date'],
                            'exper_end_name' => $experienceData['end_date'],
                            'description' => $experienceData['description'] !== 'not mentioned' ? $experienceData['description'] : null,
                        ]);
                    }
                }
            }

            // Handle Education if provided
            if ($request->has('Education')) {
                foreach ($request->input('Education') as $educationData) {
                    if (isset($educationData['degree']) && $educationData['degree'] !== 'not mentioned') {
                        Education::create([
                            'user_id' => $user->id,
                            'university_name' => $educationData['institution'],
                            'university_start_date' => $educationData['start_date'],
                            'university_end_date' => $educationData['end_date'],
                            'specialization' => $educationData['degree'],
                            'university_location' => $educationData['description'] !== 'not mentioned' ? $educationData['description'] : null,
                        ]);
                    }
                }
            }

            // Handle Skills if provided
            if ($request->has('Skills') && $request->input('Skills') !== 'not mentioned') {
                foreach ($request->input('Skills') as $skill) {
                    Skill::create([
                        'user_id' => $user->id,
                        'skill_name' => $skill,
                    ]);
                }
            }

            // Handle Certifications if provided
            if ($request->has('Certifications') && $request->input('Certifications') !== 'not mentioned') {
                foreach ($request->input('Certifications') as $certification) {
                    Certificate::create([
                        'user_id' => $user->id,
                        'certificate_name' => $certification['name'],
                        'issuing_organization' => $certification['issuing_organization'] ?? null,
                        'certificate_date' => $certification['issue_date'] ?? null,
                        'expiration_date' => $certification['expiration_date'] ?? null,
                    ]);
                }
            }

            // Handle Languages if provided
            if ($request->has('Languages') && $request->input('Languages') !== 'not mentioned') {
                foreach ($request->input('Languages') as $language) {
                    Language::create([
                        'user_id' => $user->id,
                        'language_name' => $language,
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'CV data stored successfully.'], 201);
        } catch (\Exception $e) {
            // Rollback the transaction in case of any failure
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to store CV data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
