<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SummaryResource;
use App\Models\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    public function index()
    {
        // Return summaries using a resource collection
        $summaries = Summary::where('user_id', auth()->id())->get();
        return SummaryResource::collection($summaries);
    }

    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'summary' => 'required|string',
        ]);

        // Create summary
        $summary = Summary::create([
            'user_id' => auth()->id(),
            'summary' => $request->summary,
        ]);

        // Return created summary resource
        return new SummaryResource($summary);
    }

    public function show($id)
    {
        // Find summary or fail
        $summary = Summary::findOrFail($id);

        // Ensure the user owns the summary
        if ($summary->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Return summary resource
        return new SummaryResource($summary);
    }

    public function update(Request $request, $id)
    {
        // Find summary or fail
        $summary = Summary::findOrFail($id);

        // Ensure the user owns the summary
        if ($summary->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate request
        $request->validate([
            'summary' => 'required|string',
        ]);

        // Update summary
        $summary->update($request->all());

        // Return updated summary resource
        return new SummaryResource($summary);
    }

    public function destroy($id)
    {
        // Find summary or fail
        $summary = Summary::findOrFail($id);

        // Ensure the user owns the summary
        if ($summary->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete summary
        $summary->delete();

        // Return no content response
        return response(null, 204);
    }
}
