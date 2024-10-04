<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    public function index()
    {
        return Skill::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'skill_name' => 'required|string',
            'skill_name_ar' => 'nullable|string',
        ]);

        return Skill::create([
            'user_id' => Auth::id(),
            'skill_name' => $request->skill_name,
            'skill_name_ar' => $request->skill_name_ar,
        ]);
    }

    public function storeSkills(Request $request)
    {
        // Validate the request, ensuring 'skills' is an array and each element has 'skill_name'
        $request->validate([
            'skills' => 'required|array',
            'skills.*.skill_name' => 'required|string',
            'skills.*.skill_name_ar' => 'nullable|string',
        ]);

        // Initialize an empty array to store created skills
        $createdSkills = [];

        // Loop through the array of skills and create each one
        foreach ($request->skills as $skillData) {
            $createdSkills[] = Skill::create([
                'user_id' => Auth::id(),
                'skill_name' => $skillData['skill_name'],
                'skill_name_ar' => $skillData['skill_name_ar'],
            ]);
        }

        // Return the created skills
        return response()->json([
            'message' => 'Skills created successfully.',
            'skills' => $createdSkills,
        ], 201);
    }
    public function show($id)
    {
        $skill = Skill::findOrFail($id);

        if ($skill->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($skill);
    }

    public function update(Request $request, $id)
    {
        $skill = Skill::findOrFail($id);

        if ($skill->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'skill_name' => 'required|string',
        ]);

        $skill->update($request->all());

        return response()->json($skill);
    }

    public function destroy($id)
    {
        $skill = Skill::findOrFail($id);

        if ($skill->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $skill->delete();

        return response(null, 204);
    }
}
