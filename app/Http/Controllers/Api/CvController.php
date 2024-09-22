<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'education' => $user->education ? $user->education->map(function($edu) {
                return [
                    'university_name' => $edu->university_name,
                    'university_start_date' => $edu->university_start_date,
                    'university_end_date' => $edu->university_end_date,
                    'university_location' => $edu->university_location,
                    'specialization' => $edu->specialization,
                ];
            }) : [],
            'certificates' => $user->certificates ? $user->certificates->map(function($cert) {
                return [
                    'certificate_name' => $cert->certificate_name,
                    'certificate_date' => $cert->certificate_date,
                ];
            }) : [],
            'experiences' => $user->experiences ? $user->experiences->map(function($experience) {
                return [
                    'experience_name' => $experience->exper_name,
                    'company_name' => $experience->company_name,
                    'company_location' => $experience->company_location,
                    'start_date' => $experience->exper_start_date,
                    'end_date' => $experience->exper_end_date,
                ];
            }) : [],
            'languages' => $user->languages ? $user->languages->pluck('language_name') : [],
            'references' => $user->references ? $user->references->map(function($ref) {
                return [
                    'first_name' => $ref->ref_first_name,
                    'last_name' => $ref->ref_last_name,
                    'phone' => $ref->ref_phone,
                ];
            }) : []
        ];

        return response()->json($cvData);
    }

}
