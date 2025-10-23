<?php

/**
 * Test script for Gemini AI Integration
 *
 * This script tests the AI generation service without needing a browser.
 * Run with: php test_gemini.php
 */

require __DIR__ . '/vendor/autoload.php';

// Load environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GeminiService;

echo "\n🤖 Testing Gemini AI Integration\n";
echo "================================\n\n";

// Check if API key is configured
$apiKey = config('services.gemini.api_key');
if (!$apiKey || $apiKey === 'your_gemini_api_key_here') {
    echo "❌ GEMINI_API_KEY not configured!\n";
    echo "   Please set it in your .env file.\n";
    echo "   Get your key at: https://makersuite.google.com/app/apikey\n\n";
    exit(1);
}

echo "✅ API Key configured: " . substr($apiKey, 0, 10) . "...\n\n";

// Initialize service
$gemini = new GeminiService();

// Test 1: Generate Description
echo "📝 Test 1: Generating Event Description\n";
echo "--------------------------------------\n";
echo "Title: Community Recycling Workshop\n";
echo "Type: workshop\n";
echo "Location: Central Park Community Center\n\n";

try {
    $result = $gemini->generateEventDescription(
        'Community Recycling Workshop',
        'workshop',
        'Central Park Community Center'
    );

    if ($result['success']) {
        echo "✅ Description generated successfully!\n\n";
        echo "Generated Description:\n";
        echo str_repeat("-", 80) . "\n";
        echo wordwrap($result['description'], 78) . "\n";
        echo str_repeat("-", 80) . "\n\n";

        $descriptionForFAQ = $result['description'];
    } else {
        echo "❌ Error: " . $result['error'] . "\n\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Generate FAQ
echo "❓ Test 2: Generating Event FAQ\n";
echo "-------------------------------\n\n";

try {
    $result = $gemini->generateEventFAQ(
        'Community Recycling Workshop',
        'workshop',
        $descriptionForFAQ
    );

    if ($result['success']) {
        echo "✅ FAQ generated successfully!\n\n";
        echo "Generated FAQ:\n";
        echo str_repeat("=", 80) . "\n\n";

        foreach ($result['faqs'] as $index => $faq) {
            echo "Q" . ($index + 1) . ": " . $faq['question'] . "\n";
            echo "A:  " . wordwrap($faq['answer'], 76, "\n    ") . "\n\n";
        }

        echo str_repeat("=", 80) . "\n\n";
    } else {
        echo "❌ Error: " . $result['error'] . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

echo "🎉 All tests completed!\n\n";
echo "Next steps:\n";
echo "1. Go to http://127.0.0.1:8000/events/create\n";
echo "2. Fill in event title and type\n";
echo "3. Click 'Generate with AI' button\n";
echo "4. Enjoy your AI-powered event creation! ✨\n\n";
