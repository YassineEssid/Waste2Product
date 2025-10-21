<?php

/**
 * Debug test for Gemini API
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

echo "\n🔍 Debug Test - Gemini API\n";
echo "===========================\n\n";

$apiKey = config('services.gemini.api_key');
$apiUrl = config('services.gemini.api_url');

echo "API Key: " . substr($apiKey, 0, 15) . "...\n";
echo "API URL: " . $apiUrl . "\n\n";

$prompt = "Write a short 2-sentence description for a community recycling workshop.";

echo "📤 Sending request...\n\n";

try {
    $response = Http::withOptions([
        'verify' => false, // Disable SSL for local testing
    ])->withHeaders([
        'Content-Type' => 'application/json',
    ])->post($apiUrl . '?key=' . $apiKey, [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 1024,
        ],
    ]);

    echo "Status Code: " . $response->status() . "\n";
    echo "Response Body:\n";
    echo str_repeat("-", 80) . "\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n";
    echo str_repeat("-", 80) . "\n\n";

    if ($response->successful()) {
        $data = $response->json();

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            echo "✅ SUCCESS!\n\n";
            echo "Generated Text:\n";
            echo $data['candidates'][0]['content']['parts'][0]['text'] . "\n\n";
        } else {
            echo "❌ Invalid response structure\n";
            echo "Available keys: " . implode(', ', array_keys($data)) . "\n\n";
        }
    } else {
        echo "❌ API request failed!\n";
        echo "Error: " . $response->body() . "\n\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}
