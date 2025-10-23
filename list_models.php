<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = config('services.gemini.api_key');

echo "\n📋 Listing Available Gemini Models\n";
echo "===================================\n\n";

try {
    $response = Http::withOptions(['verify' => false])
        ->get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $apiKey);

    if ($response->successful()) {
        $data = $response->json();

        if (isset($data['models'])) {
            echo "✅ Found " . count($data['models']) . " models:\n\n";

            foreach ($data['models'] as $model) {
                echo "• " . $model['name'] . "\n";
                if (isset($model['supportedGenerationMethods'])) {
                    echo "  Methods: " . implode(', ', $model['supportedGenerationMethods']) . "\n";
                }
                echo "\n";
            }
        }
    } else {
        echo "Error: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
