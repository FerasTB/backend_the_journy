<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index()
    {
        return Certificate::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'certificate_name' => 'required|string',
            'certificate_date' => 'required|date',
        ]);

        return Certificate::create([
            'user_id' => Auth::id(),
            'certificate_name' => $request->certificate_name,
            'certificate_date' => $request->certificate_date,
        ]);
    }

    public function show($id)
    {
        $certificate = Certificate::findOrFail($id);
    
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        return response()->json($certificate);
    }
    
    public function update(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);
    
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $request->validate([
            'certificate_name' => 'required|string',
            'certificate_date' => 'required|date',
        ]);
    
        $certificate->update($request->all());
    
        return response()->json($certificate);
    }
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
    
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $certificate->delete();
    
        return response(null, 204);
    }
    }
