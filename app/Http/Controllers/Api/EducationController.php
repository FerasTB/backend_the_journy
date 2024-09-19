<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    public function index()
    {
        return Education::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'university_name' => 'required|string',
            'university_start_date' => 'required|date',
            'university_end_date' => 'nullable|date',
            'university_location' => 'required|string',
            'specialization' => 'required|string',
        ]);

        return Education::create([
            'user_id' => Auth::id(),
            'university_name' => $request->university_name,
            'university_start_date' => $request->university_start_date,
            'university_end_date' => $request->university_end_date,
            'university_location' => $request->university_location,
            'specialization' => $request->specialization,
        ]);
    }
    public function show($id)
    {
        $education = Education::findOrFail($id);
    
        if ($education->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        return response()->json($education);
    }
    
    public function update(Request $request, $id)
    {
        $education = Education::findOrFail($id);
    
        if ($education->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $request->validate([
            'university_name' => 'required|string',
            'university_start_date' => 'required|date',
            'university_end_date' => 'nullable|date',
            'university_location' => 'required|string',
            'specialization' => 'required|string',
        ]);
    
        $education->update($request->all());
    
        return response()->json($education);
    }
    
    public function destroy($id)
    {
        $education = Education::findOrFail($id);
    
        if ($education->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $education->delete();
    
        return response(null, 204);
    }
    
}
