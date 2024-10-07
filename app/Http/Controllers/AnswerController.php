<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Http\Requests\StoreAnswerRequest;
use App\Http\Requests\UpdateAnswerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'video_file' => 'required|file|mimes:mp4,mov,avi,wmv|max:204800', // Max 200MB
        ]);

        $userId = auth()->id();

        $file = $request->file('video_file');

        $uniqueFileName = uniqid() . '.' . $file->getClientOriginalExtension();

        $videoPath = $file->storeAs("answers/videos/{$userId}", $uniqueFileName, 'public');
        $answer = Answer::create([
            'user_id' => $request->user()->id,
            'question_id' => $request->input('question_id'),
            'video_path' => $videoPath,
        ]);

        return response()->json($answer, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Answer $answer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnswerRequest $request, Answer $answer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Answer $answer)
    {
        //
    }

    public function getUserAnswers(User $user)
    {
        $answers = Answer::where('user_id', $user->id)
            ->with('question')
            ->get();

        return response()->json($answers, Response::HTTP_OK);
    }
}
