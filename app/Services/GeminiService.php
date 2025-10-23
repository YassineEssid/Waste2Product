<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->apiUrl = config('services.gemini.api_url');
    }

    /**
     * Generate event description based on title and type
     */
    public function generateEventDescription(string $title, string $type, ?string $location = null): array
    {
        $prompt = $this->buildDescriptionPrompt($title, $type, $location);

        try {
            $response = $this->callGeminiAPI($prompt);

            return [
                'success' => true,
                'description' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('Gemini API Error (Description): ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to generate description. Please try again.',
            ];
        }
    }

    /**
     * Generate FAQ for an event
     */
    public function generateEventFAQ(string $title, string $type, string $description): array
    {
        $prompt = $this->buildFAQPrompt($title, $type, $description);

        try {
            $response = $this->callGeminiAPI($prompt);
            $faqs = $this->parseFAQResponse($response);

            return [
                'success' => true,
                'faqs' => $faqs,
            ];
        } catch (\Exception $e) {
            Log::error('Gemini API Error (FAQ): ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to generate FAQ. Please try again.',
            ];
        }
    }

    /**
     * Build prompt for description generation
     */
    protected function buildDescriptionPrompt(string $title, string $type, ?string $location): string
    {
        $typeDescriptions = [
            'workshop' => 'a hands-on learning session where participants acquire practical skills',
            'conference' => 'an informative gathering with presentations and discussions',
            'cleanup' => 'a community effort to clean and preserve the environment',
            'exhibition' => 'a showcase of innovative ideas, products, or achievements',
            'training' => 'an educational program to develop specific competencies',
            'networking' => 'a professional gathering to build connections and share knowledge',
        ];

        $typeContext = $typeDescriptions[$type] ?? 'a community event';
        $locationText = $location ? " taking place at {$location}" : '';

        return <<<PROMPT
You are an expert event organizer specializing in environmental and sustainability initiatives.

Generate an engaging and detailed event description for:
- Event Title: "{$title}"
- Event Type: {$type} ({$typeContext})
{$locationText}

Requirements:
1. Write 2-3 paragraphs (150-200 words total)
2. Make it inspiring and action-oriented
3. Highlight the environmental impact and community benefits
4. Use a professional yet friendly tone
5. Focus on waste reduction, recycling, upcycling, or sustainability themes
6. Include what participants will learn or gain
7. Make it specific and concrete, not generic

Write ONLY the description text, no titles or labels.
PROMPT;
    }

    /**
     * Build prompt for FAQ generation
     */
    protected function buildFAQPrompt(string $title, string $type, string $description): string
    {
        return <<<PROMPT
You are an event coordinator creating helpful FAQ content for participants.

Based on this event:
- Title: "{$title}"
- Type: {$type}
- Description: {$description}

Generate 5 frequently asked questions with clear, concise answers.

Requirements:
1. Focus on practical concerns: timing, materials needed, prerequisites, registration, location
2. Keep answers brief (2-3 sentences each)
3. Be specific and helpful
4. Address common participant concerns
5. Format each as "Q: [question]\nA: [answer]"

Generate exactly 5 Q&A pairs, separated by blank lines.
PROMPT;
    }

    /**
     * Call Gemini API
     */
    protected function callGeminiAPI(string $prompt): string
    {
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification for local development
        ])->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '?key=' . $this->apiKey, [
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
                'maxOutputTokens' => 2048, // Increased for FAQ generation
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Gemini API HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('API request failed: ' . $response->body());
        }

        $data = $response->json();

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Gemini API Invalid Response Structure', [
                'response' => $data,
                'has_candidates' => isset($data['candidates']),
                'candidates_count' => isset($data['candidates']) ? count($data['candidates']) : 0
            ]);
            throw new \Exception('Invalid API response structure');
        }

        return trim($data['candidates'][0]['content']['parts'][0]['text']);
    }

    /**
     * Parse FAQ response into structured array
     */
    protected function parseFAQResponse(string $response): array
    {
        $faqs = [];
        $blocks = preg_split('/\n\s*\n/', trim($response));

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) {
                continue;
            }

            // Match Q: ... A: ... pattern
            if (preg_match('/Q:\s*(.+?)\s*A:\s*(.+)/s', $block, $matches)) {
                $faqs[] = [
                    'question' => trim($matches[1]),
                    'answer' => trim($matches[2]),
                ];
            }
        }

        return $faqs;
    }

    /**
     * Generic method to generate content from any prompt
     * Used by marketplace AI services
     *
     * @param string $prompt The prompt to send to Gemini
     * @param array $config Optional generation config
     * @return array ['success' => bool, 'text' => string, 'error' => string]
     */
    public function generateContent(string $prompt, array $config = []): array
    {
        try {
            $text = $this->callGeminiAPI($prompt);

            return [
                'success' => true,
                'text' => $text,
                'error' => ''
            ];
        } catch (\Exception $e) {
            Log::error('Gemini API Error (Generic): ' . $e->getMessage());

            return [
                'success' => false,
                'text' => '',
                'error' => $e->getMessage()
            ];
        }
    }
}
