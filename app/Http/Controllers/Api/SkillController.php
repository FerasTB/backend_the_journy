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
        ]);

        return Skill::create([
            'user_id' => Auth::id(),
            'skill_name' => $request->skill_name,
        ]);
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

