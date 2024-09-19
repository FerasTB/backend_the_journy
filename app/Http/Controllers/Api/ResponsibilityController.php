<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Responsibility;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{
    public function index()
    {
        return Responsibility::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'exper_id' => 'required|exists:experiences,id',
            'responsability_name' => 'required|string',
        ]);

        return Responsibility::create($request->all());
    }
    public function show($id)
    {
        $responsibility = Responsibility::findOrFail($id);
    
        return response()->json($responsibility);
    }
    
    public function update(Request $request, $id)
    {
        $responsibility = Responsibility::findOrFail($id);
    
        $request->validate([
            'responsability_name' => 'required|string',
        ]);
    
        $responsibility->update($request->all());
    
        return response()->json($responsibility);
    }
    
    public function destroy($id)
    {
        $responsibility = Responsibility::findOrFail($id);
    
        $responsibility->delete();
    
        return response(null, 204);
    }
    
}
