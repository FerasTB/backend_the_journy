<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    public function index()
    {
        return Language::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'language_name' => 'required|string',
        ]);

        return Language::create([
            'user_id' => Auth::id(),
            'language_name' => $request->language_name,
        ]);
    }


    public function show($id)
{
    $language = Language::findOrFail($id);

    if ($language->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    return response()->json($language);
}

public function update(Request $request, $id)
{
    $language = Language::findOrFail($id);

    if ($language->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'language_name' => 'required|string',
    ]);

    $language->update($request->all());

    return response()->json($language);
}

public function destroy($id)
{
    $language = Language::findOrFail($id);

    if ($language->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $language->delete();

    return response(null, 204);
}

}
