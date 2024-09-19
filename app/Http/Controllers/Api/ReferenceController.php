<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferenceController extends Controller
{
    public function index()
    {
        return Reference::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_first_name' => 'required|string',
            'ref_last_name' => 'required|string',
            'ref_phone' => 'required|string',
        ]);

        return Reference::create([
            'user_id' => Auth::id(),
            'ref_first_name' => $request->ref_first_name,
            'ref_last_name' => $request->ref_last_name,
            'ref_phone' => $request->ref_phone,
        ]);
    }
    public function show($id)
    {
        $reference = Reference::findOrFail($id);
    
        if ($reference->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        return response()->json($reference);
    }
    
    public function update(Request $request, $id)
    {
        $reference = Reference::findOrFail($id);
    
        if ($reference->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $request->validate([
            'ref_first_name' => 'required|string',
            'ref_last_name' => 'required|string',
            'ref_phone' => 'required|string',
        ]);
    
        $reference->update($request->all());
    
        return response()->json($reference);
    }
    
    public function destroy($id)
    {
        $reference = Reference::findOrFail($id);
    
        if ($reference->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $reference->delete();
    
        return response(null, 204);
    }
    }
