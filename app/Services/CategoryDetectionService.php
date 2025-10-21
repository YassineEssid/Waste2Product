<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service for detecting marketplace item category using Gemini AI
 * Analyzes user input and suggests optimal category, title, and keywords
 */
class CategoryDetectionService
{
    protected $gemini;

    // Available categories in the marketplace
    protected $categories = [
        'furniture' => 'Mobilier (tables, chaises, armoires, lits, canapés)',
        'electronics' => 'Électronique (ordinateurs, téléphones, appareils)',
        'clothing' => 'Vêtements (habits, chaussures, accessoires)',
        'books' => 'Livres (romans, manuels, magazines)',
        'toys' => 'Jouets (jeux, peluches, puzzles)',
        'tools' => 'Outils (bricolage, jardinage, réparation)',
        'decorative' => 'Décoration (cadres, vases, objets décoratifs)',
        'appliances' => 'Électroménager (cuisine, nettoyage)',
        'other' => 'Autre (divers articles)'
    ];

    // Available conditions
    protected $conditions = [
        'excellent' => 'Excellent état (comme neuf)',
        'good' => 'Bon état (légères traces d\'usage)',
        'fair' => 'État correct (usure visible)',
        'needs_repair' => 'À réparer (nécessite restauration)'
    ];

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Detect category and generate suggestions from user description
     *
     * @param string $description User's description of the item
     * @return array ['success' => bool, 'data' => array, 'error' => string]
     */
    public function detectCategory(string $description): array
    {
        if (empty(trim($description))) {
            return [
                'success' => false,
                'data' => [],
                'error' => 'La description ne peut pas être vide'
            ];
        }

        try {
            $prompt = $this->buildPrompt($description);
            $response = $this->gemini->generateContent($prompt);

            if (!$response['success']) {
                return [
                    'success' => false,
                    'data' => [],
                    'error' => $response['error'] ?? 'Erreur lors de la détection'
                ];
            }

            $data = $this->parseResponse($response['text']);

            if (empty($data)) {
                return [
                    'success' => false,
                    'data' => [],
                    'error' => 'Impossible d\'analyser la réponse'
                ];
            }

            return [
                'success' => true,
                'data' => $data,
                'error' => ''
            ];
        } catch (\Exception $e) {
            Log::error('Category detection failed', [
                'description' => $description,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'data' => [],
                'error' => 'Une erreur est survenue lors de l\'analyse'
            ];
        }
    }

    /**
     * Build the prompt for Gemini API
     */
    protected function buildPrompt(string $description): string
    {
        $categoriesList = '';
        foreach ($this->categories as $key => $label) {
            $categoriesList .= "- {$key}: {$label}\n";
        }

        $conditionsList = '';
        foreach ($this->conditions as $key => $label) {
            $conditionsList .= "- {$key}: {$label}\n";
        }

        return <<<PROMPT
Tu es un assistant IA spécialisé dans la classification d'articles de marketplace de réutilisation.

Analyse cette description d'article fournie par un utilisateur:
"{$description}"

Catégories disponibles:
{$categoriesList}

États disponibles:
{$conditionsList}

Ta mission:
1. Identifier la catégorie la plus appropriée
2. Suggérer un titre optimisé et accrocheur (maximum 60 caractères)
3. Estimer l'état probable de l'article
4. Extraire 3-5 mots-clés pertinents
5. Donner un niveau de confiance (0-100%)

Format de réponse EXACT (respecte ce format strictement):
CATEGORY: [clé de la catégorie]
TITLE: [titre suggéré]
CONDITION: [clé de l'état]
KEYWORDS: [mot1, mot2, mot3, ...]
CONFIDENCE: [0-100]
REASONING: [courte explication en 1-2 phrases]

Règles:
- Utilise UNIQUEMENT les clés exactes des catégories et états
- Le titre doit être attractif et informatif
- Les mots-clés doivent être en français
- Sois précis et concis

Génère maintenant ta réponse:
PROMPT;
    }

    /**
     * Parse the AI response into structured data
     */
    protected function parseResponse(string $text): array
    {
        $data = [
            'category' => 'other',
            'title' => '',
            'condition' => 'good',
            'keywords' => [],
            'confidence' => 0,
            'reasoning' => ''
        ];

        // Extract CATEGORY
        if (preg_match('/CATEGORY:\s*(\w+)/i', $text, $matches)) {
            $category = strtolower(trim($matches[1]));
            if (array_key_exists($category, $this->categories)) {
                $data['category'] = $category;
            }
        }

        // Extract TITLE
        if (preg_match('/TITLE:\s*(.+?)(?=\n[A-Z]+:|$)/is', $text, $matches)) {
            $data['title'] = trim($matches[1]);
        }

        // Extract CONDITION
        if (preg_match('/CONDITION:\s*(\w+)/i', $text, $matches)) {
            $condition = strtolower(trim($matches[1]));
            if (array_key_exists($condition, $this->conditions)) {
                $data['condition'] = $condition;
            }
        }

        // Extract KEYWORDS
        if (preg_match('/KEYWORDS:\s*(.+?)(?=\n[A-Z]+:|$)/is', $text, $matches)) {
            $keywordsStr = trim($matches[1]);
            $keywords = array_map('trim', explode(',', $keywordsStr));
            $data['keywords'] = array_filter($keywords);
        }

        // Extract CONFIDENCE
        if (preg_match('/CONFIDENCE:\s*(\d+)/i', $text, $matches)) {
            $data['confidence'] = (int) $matches[1];
        }

        // Extract REASONING
        if (preg_match('/REASONING:\s*(.+?)$/is', $text, $matches)) {
            $data['reasoning'] = trim($matches[1]);
        }

        // Add human-readable labels
        $data['category_label'] = $this->categories[$data['category']] ?? 'Autre';
        $data['condition_label'] = $this->conditions[$data['condition']] ?? 'Bon état';

        return $data;
    }

    /**
     * Get all available categories
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Get all available conditions
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * Validate if a category key is valid
     */
    public function isValidCategory(string $category): bool
    {
        return array_key_exists($category, $this->categories);
    }

    /**
     * Validate if a condition key is valid
     */
    public function isValidCondition(string $condition): bool
    {
        return array_key_exists($condition, $this->conditions);
    }
}
