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
use Carbon\Carbon;
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
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'city' => $user->city,
                'website_url' => $user->website_url,
                'linkedin_url' => $user->linkedin_url,
            ],
            'job_title' => $user->cv ? [
                'id' => $user->cv->id,
                'job_title' => $user->cv->job_title
            ] : null,
            'summary' => $user->summary ? [
                'id' => $user->summary->id,
                'summary' => $user->summary->summary
            ] : null,
            'skills' => $user->skills ? $user->skills->map(function ($skill) {
                return [
                    'id' => $skill->id,
                    'skill_name' => $skill->skill_name,
                ];
            }) : [],
            'education' => $user->education ? $user->education->map(function ($edu) {
                return [
                    'id' => $edu->id,
                    'university_name' => $edu->university_name,
                    'university_start_date' => $edu->university_start_date,
                    'university_end_date' => $edu->university_end_date,
                    'university_location' => $edu->university_location,
                    'specialization' => $edu->specialization,
                ];
            }) : [],
            'certificates' => $user->certificates ? $user->certificates->map(function ($cert) {
                return [
                    'id' => $cert->id,
                    'certificate_name' => $cert->certificate_name,
                    'certificate_date' => $cert->certificate_date,
                ];
            }) : [],
            'experiences' => $user->experiences ? $user->experiences->map(function ($experience) {
                return [
                    'id' => $experience->id,
                    'experience_name' => $experience->exper_name,
                    'company_name' => $experience->company_name,
                    'company_location' => $experience->company_location,
                    'start_date' => $experience->exper_start_date,
                    'end_date' => $experience->exper_end_date,
                ];
            }) : [],
            'languages' => $user->languages ? $user->languages->map(function ($language) {
                return [
                    'id' => $language->id,
                    'language_name' => $language->language_name,
                ];
            }) : [],
            'references' => $user->references ? $user->references->map(function ($ref) {
                return [
                    'id' => $ref->id,
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
                        // Validate and format the start and end dates
                        $startDate = $this->formatDate($experienceData['start_date']);
                        $endDate = $this->formatDate($experienceData['end_date']);

                        if (!$startDate) {
                            return response()->json(['error' => 'Invalid start date format: ' . $experienceData['start_date']], 422);
                        }

                        if ($endDate === false && $experienceData['end_date'] !== 'not mentioned') {
                            return response()->json(['error' => 'Invalid end date format: ' . $experienceData['end_date']], 422);
                        }

                        Experience::create([
                            'user_id' => $user->id,
                            'exper_name' => $experienceData['title'],
                            'company_name' => $experienceData['company'],
                            'exper_start_date' => $startDate,
                            'exper_end_name' => $endDate,
                            'description' => $experienceData['description'] !== 'not mentioned' ? $experienceData['description'] : null,
                        ]);
                    }
                }
            }

            // Handle Education if provided
            if ($request->has('Education')) {
                foreach ($request->input('Education') as $educationData) {
                    if (isset($educationData['degree']) && $educationData['degree'] !== 'not mentioned') {
                        // Validate and format the start and end dates
                        $startDate = $this->formatDate($educationData['start_date']);
                        $endDate = $this->formatDate($educationData['end_date']);

                        if (!$startDate) {
                            return response()->json(['error' => 'Invalid start date format: ' . $educationData['start_date']], 422);
                        }

                        if ($endDate === false && $educationData['end_date'] !== 'not mentioned') {
                            return response()->json(['error' => 'Invalid end date format: ' . $educationData['end_date']], 422);
                        }

                        Education::create([
                            'user_id' => $user->id,
                            'university_name' => $educationData['institution'],
                            'university_start_date' => $startDate,
                            'university_end_date' => $endDate,
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

    private function formatDate($date)
    {
        try {
            // Check if the date is not 'not mentioned' and try to parse it
            if ($date && $date !== 'not mentioned') {
                // Try parsing the date using Carbon
                return Carbon::parse($date)->toDateString(); // Convert to YYYY-MM-DD format
            }
        } catch (\Exception $e) {
            // Return false if the date format is invalid
            return false;
        }

        // Return null if the date is 'not mentioned'
        return null;
    }
}
