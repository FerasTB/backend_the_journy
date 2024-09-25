<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class aicontroller extends Controller
{
    public function handleApiRequest(Request $request)
    {
        // The experience section you want to send to the API
        $experienceText = $request->input('experience_section');

        // Create the prompt for the API
        $prompt = [
            'experience' => "Take the following text from the 'Experience' section of a CV and modify it to:\n\n" .
                "Start each bullet point or sentence with an action verb.\n" .
                "Ensure clear and concise wording without unnecessary details.\n" .
                "Emphasize quantifiable achievements, metrics, or key results when possible.\n" .
                "Use industry-relevant keywords to enhance ATS compatibility.\n" .
                "Avoid passive voice or vague descriptions.\n" .
                "Return the response as a JSON object with two keys:\n\n" .
                "'experience': containing the adjusted experience section text.\n" .
                "'note': mentioning any suggestions or feedback, such as missing metrics or improvements that could be made to strengthen the experience.\n\n" .
                "Here’s the experience section text:\n\n" .
                "\"$experienceText\"\n" .
                "Return the response in JSON format."
        ];

        // Make the API call (replace with your actual API URL)
        $response = Http::withOptions([
            'curl' => [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2, // Forces TLS 1.2
            ]
        ])->post('https://dockerjourney.flaamingo.com/llm-response', [
            'message' => $prompt
        ]);

        // Check if the response is successful
        if ($response->successful()) {

            // Convert response to JSON
            $json = $response->json();

            // Check if the JSON contains the keys 'experience' and 'note'
            if (isset($json['experience']) && isset($json['note'])) {

                // Return the response from the API
                return response()->json([
                    'success' => true,
                    'message' => 'Experience section processed successfully',
                    'data' => $json
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Required keys not found in the JSON response'
                ], 400);
            }
        } else {
            // Handle unsuccessful response
            return response()->json([
                'success' => false,
                'message' => 'API request failed',
                'error' => $response->body() // Log or return the error
            ], 500);
        }
    }
}
