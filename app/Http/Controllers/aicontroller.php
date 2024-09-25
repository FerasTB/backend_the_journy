<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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
            'message' => <<<EOT
Take the following text from the 'Experience' section of a CV and modify it to:

Start each bullet point or sentence with an action verb.
Ensure clear and concise wording without unnecessary details.
Emphasize quantifiable achievements, metrics, or key results when possible.
Use industry-relevant keywords to enhance ATS compatibility.
Avoid passive voice or vague descriptions.
Return the response as a JSON object with two keys:

experience: containing the adjusted experience section text.

Here is the experience section text:

"$experienceText"

Return the response in JSON format.
EOT
        ];

        // Initialize Guzzle Client
        $client = new Client([
            'base_uri' => 'https://dockerjourney.flaamingo.com', // Replace with your API base URL
            'timeout'  => 10.0, // Set the request timeout
        ]);

        try {
            // Send the request using Guzzle
            $response = $client->post('/llm-response', [
                'json' => $prompt,  // Send the prompt as JSON
                'verify' => false,  // Disable SSL verification (not recommended for production)
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            // Get the response body and decode it as JSON
            $json = json_decode($response->getBody()->getContents(), true);
            // return $json;
            // Check if the response has 'llm_response' key
            if (isset($json['llm_response'])) {
                // return $json['llm_response'];
                // The 'llm_response' is JSON but as a string, so decode it again
                $decodedLlmResponse = json_decode($json['llm_response'], true);
                // return $decodedLlmResponse;
                // Check if the JSON contains the keys 'experience' and 'note'
                if (isset($decodedLlmResponse['experience'])) {
                    // Return the processed response
                    return response()->json([
                        'success' => true,
                        'message' => 'Experience section processed successfully',
                        'data' => $decodedLlmResponse
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Required keys not found in the decoded llm_response'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'llm_response key not found in the initial JSON response'
                ], 400);
            }
        } catch (RequestException $e) {
            // Handle Guzzle request exception
            return response()->json([
                'success' => false,
                'message' => 'API request failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
