<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

echo "\n🔍 Debug Test - FAQ Generation\n";
echo "================================\n\n";

$apiKey = config('services.gemini.api_key');
$apiUrl = config('services.gemini.api_url');

$prompt = <<<PROMPT
You are an event coordinator creating helpful FAQ content for participants.

Based on this event:
- Title: "Community Recycling Workshop"
- Type: workshop
- Description: Join us for an engaging workshop about recycling.

Generate 5 frequently asked questions with clear, concise answers.

Requirements:
1. Focus on practical concerns: timing, materials needed, prerequisites, registration, location
2. Keep answers brief (2-3 sentences each)
3. Be specific and helpful
4. Address common participant concerns
5. Format each as "Q: [question]\nA: [answer]"

Generate exactly 5 Q&A pairs, separated by blank lines.
PROMPT;

echo "📤 Sending FAQ request...\n\n";

try {
    $response = Http::withOptions(['verify' => false])
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post($apiUrl . '?key=' . $apiKey, [
            'contents' => [[
                'parts' => [['text' => $prompt]]
            ]],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
            ],
        ]);

    if ($response->successful()) {
        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'];

        echo "✅ Generated Text:\n";
        echo str_repeat("-", 80) . "\n";
        echo $text . "\n";
        echo str_repeat("-", 80) . "\n\n";

        // Test parsing
        echo "🔍 Parsing FAQs...\n\n";
        $faqs = [];
        $blocks = preg_split('/\n\s*\n/', trim($text));

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) {
                continue;
            }

            if (preg_match('/Q:\s*(.+?)\s*A:\s*(.+)/s', $block, $matches)) {
                $faqs[] = [
                    'question' => trim($matches[1]),
                    'answer' => trim($matches[2]),
                ];
            }
        }

        echo "Found " . count($faqs) . " FAQ pairs:\n\n";
        foreach ($faqs as $index => $faq) {
            echo "Q" . ($index + 1) . ": " . $faq['question'] . "\n";
            echo "A:  " . $faq['answer'] . "\n\n";
        }

    } else {
        echo "❌ Error: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
