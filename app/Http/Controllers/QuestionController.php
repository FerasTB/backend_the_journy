<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get all questions
        $questions = Question::all();

        // Get question IDs answered by the user
        $answeredQuestionIds = $user->answers()->pluck('question_id')->toArray();

        // Prepare the response data
        $data = $questions->map(function ($question) use ($answeredQuestionIds) {
            return [
                'id' => $question->id,
                'answered' => in_array($question->id, $answeredQuestionIds),
            ];
        });

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'video_file' => 'required|file|mimes:mp4,mov,avi,wmv|max:20480', // Max 20MB
        ]);

        $videoPath = $request->file('video_file')->store('questions/videos', 'public');

        $question = Question::create([
            'text' => $request->input('text'),
            'video_path' => $videoPath,
        ]);

        return response()->json($question, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        return response()->json($question, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        //
    }
}
