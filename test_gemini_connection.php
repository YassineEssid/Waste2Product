<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Connexion Gemini API ===\n\n";

// Test de la configuration
$apiKey = config('services.gemini.api_key');
$apiUrl = config('services.gemini.api_url');

echo "Configuration:\n";
echo "- API Key: " . (empty($apiKey) ? "❌ NON DÉFINI" : "✅ Défini (" . substr($apiKey, 0, 10) . "...)") . "\n";
echo "- API URL: " . ($apiUrl ?? "❌ NON DÉFINI") . "\n\n";

if (empty($apiKey)) {
    echo "❌ ERREUR: GEMINI_API_KEY n'est pas défini dans .env\n";
    exit(1);
}

// Test d'appel simple
echo "Test d'appel à l'API Gemini...\n\n";

try {
    $prompt = "Réponds en JSON avec exactement ce format: {\"message\": \"Bonjour\"}";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl . '?key=' . $apiKey,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    echo "HTTP Code: $httpCode\n";

    if ($error) {
        echo "❌ Erreur cURL: $error\n";
        exit(1);
    }

    if ($httpCode !== 200) {
        echo "❌ Erreur HTTP $httpCode\n";
        echo "Réponse: " . substr($response, 0, 500) . "\n";
        exit(1);
    }

    $result = json_decode($response, true);

    if (isset($result['error'])) {
        echo "❌ Erreur API: " . $result['error']['message'] . "\n";
        exit(1);
    }

    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $result['candidates'][0]['content']['parts'][0]['text'];
        echo "✅ Réponse reçue:\n";
        echo $text . "\n\n";
        echo "✅ Connexion à Gemini réussie !\n";
    } else {
        echo "❌ Format de réponse inattendu:\n";
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    exit(1);
}
