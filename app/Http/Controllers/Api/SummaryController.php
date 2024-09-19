<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    public function index()
    {
        return Summary::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'summary' => 'required|string',
        ]);

        return Summary::create([
            'user_id' => Auth::id(),
            'summary' => $request->summary,
        ]);
    }
    public function show($id)
    {
        $summary = Summary::findOrFail($id);
    
        if ($summary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        return response()->json($summary);
    }
    
    public function update(Request $request, $id)
    {
        $summary = Summary::findOrFail($id);
    
        if ($summary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $request->validate([
            'summary' => 'required|string',
        ]);
    
        $summary->update($request->all());
    
        return response()->json($summary);
    }
    
    public function destroy($id)
    {
        $summary = Summary::findOrFail($id);
    
        if ($summary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $summary->delete();
    
        return response(null, 204);
    }
    
}
