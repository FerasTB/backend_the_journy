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
                ],
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ]);

            // Get the response body and decode it as JSON
            $json = json_decode($response->getBody()->getContents(), true);

            // Check if the JSON contains the keys 'experience' and 'note'
            if (isset($json['experience']) && isset($json['note'])) {
                // Return the processed response
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
