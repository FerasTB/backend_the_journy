<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExperienceController extends Controller
{
    public function index()
    {
        return Experience::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'exper_name' => 'required|string',
            'exper_start_date' => 'required|date',
            'exper_end_name' => 'nullable|date',
            'company_name' => 'required|string',
            'company_location' => 'required|string',
        ]);

        return Experience::create([
            'user_id' => Auth::id(),
            'exper_name' => $request->exper_name,
            'exper_start_date' => $request->exper_start_date,
            'exper_end_name' => $request->exper_end_name,
            'company_name' => $request->company_name,
            'company_location' => $request->company_location,
        ]);
    }
    public function show($id)
    {
        $experience = Experience::findOrFail($id);

        if ($experience->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($experience);
    }

    public function update(Request $request, $id)
    {
        $experience = Experience::findOrFail($id);

        if ($experience->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'exper_name' => 'required|string',
            'exper_start_date' => 'required|date',
            'exper_end_date' => 'nullable|date',
            'company_name' => 'required|string',
            'company_location' => 'required|string',
        ]);

        $experience->update($request->all());

        return response()->json($experience);
    }

    public function destroy($id)
    {
        $experience = Experience::findOrFail($id);

        if ($experience->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $experience->delete();

        return response(null, 204);
    }

    public function storeExperienceWithResponsibilities(Request $request)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Validate the incoming request
            $request->validate([
                'exper_name' => 'required|string',
                'exper_start_date' => 'required|date',
                'exper_end_name' => 'nullable|date',
                'company_name' => 'required|string',
                'company_location' => 'required|string',
                'responsibilities' => 'nullable|array',
                'responsibilities.*.responsability_name' => 'required_with:responsibilities|string'
            ]);

            // Create the experience
            $experience = Experience::create([
                'user_id' => Auth::id(),
                'exper_name' => $request->exper_name,
                'exper_start_date' => $request->exper_start_date,
                'exper_end_name' => $request->exper_end_name,
                'company_name' => $request->company_name,
                'company_location' => $request->company_location,
            ]);

            // Check if responsibilities were provided and create them
            if ($request->has('responsibilities')) {
                foreach ($request->responsibilities as $responsibilityData) {
                    Responsibility::create([
                        'exper_id' => $experience->id,
                        'responsability_name' => $responsibilityData['responsability_name'],
                    ]);
                }
            }

            // Commit the transaction (save changes to the database)
            DB::commit();

            // Return the created experience and responsibilities
            return response()->json([
                'message' => 'Experience and responsibilities created successfully.',
                'experience' => $experience
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            // Return an error response
            return response()->json([
                'message' => 'Error saving experience or responsibilities.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
