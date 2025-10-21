<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service for suggesting optimal prices for marketplace items using Gemini AI
 * Analyzes item details and market conditions to recommend pricing
 */
class PriceSuggestionService
{
    protected $gemini;

    // Base price references by category (in EUR)
    protected $categoryBasePrices = [
        'furniture' => ['min' => 20, 'max' => 500, 'avg' => 150],
        'electronics' => ['min' => 10, 'max' => 800, 'avg' => 200],
        'clothing' => ['min' => 5, 'max' => 100, 'avg' => 25],
        'books' => ['min' => 2, 'max' => 50, 'avg' => 10],
        'toys' => ['min' => 5, 'max' => 80, 'avg' => 20],
        'tools' => ['min' => 10, 'max' => 300, 'avg' => 60],
        'decorative' => ['min' => 5, 'max' => 200, 'avg' => 40],
        'appliances' => ['min' => 20, 'max' => 400, 'avg' => 100],
        'other' => ['min' => 5, 'max' => 200, 'avg' => 50],
    ];

    // Condition multipliers
    protected $conditionMultipliers = [
        'excellent' => 1.0,      // 100% of value
        'good' => 0.75,          // 75% of value
        'fair' => 0.50,          // 50% of value
        'needs_repair' => 0.25,  // 25% of value
    ];

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Suggest optimal price for an item
     *
     * @param string $title Item title
     * @param string $description Item description
     * @param string $category Item category
     * @param string $condition Item condition
     * @param array $keywords Optional keywords
     * @return array ['success' => bool, 'data' => array, 'error' => string]
     */
    public function suggestPrice(
        string $title,
        string $description,
        string $category,
        string $condition,
        array $keywords = []
    ): array {
        try {
            // Get base price range from category
            $basePriceRange = $this->categoryBasePrices[$category] ?? $this->categoryBasePrices['other'];

            // Build prompt for AI analysis
            $prompt = $this->buildPrompt(
                $title,
                $description,
                $category,
                $condition,
                $keywords,
                $basePriceRange
            );

            // Call Gemini API
            $response = $this->gemini->generateContent($prompt);

            if (!$response['success']) {
                // Fallback to basic calculation
                return $this->calculateBasicPrice($category, $condition);
            }

            // Parse AI response
            $data = $this->parseResponse($response['text'], $basePriceRange, $condition);

            if (empty($data)) {
                // Fallback to basic calculation
                return $this->calculateBasicPrice($category, $condition);
            }

            return [
                'success' => true,
                'data' => $data,
                'error' => ''
            ];
        } catch (\Exception $e) {
            Log::error('Price suggestion failed', [
                'title' => $title,
                'category' => $category,
                'error' => $e->getMessage()
            ]);

            // Fallback to basic calculation
            return $this->calculateBasicPrice($category, $condition);
        }
    }

    /**
     * Build the prompt for Gemini API
     */
    protected function buildPrompt(
        string $title,
        string $description,
        string $category,
        string $condition,
        array $keywords,
        array $basePriceRange
    ): string {
        $categoryFr = $this->translateCategory($category);
        $conditionFr = $this->translateCondition($condition);
        $keywordsStr = !empty($keywords) ? implode(', ', $keywords) : 'aucun';

        return <<<PROMPT
Tu es un expert en évaluation d'articles d'occasion pour une marketplace de réutilisation écologique.

Article à évaluer:
- Titre: {$title}
- Description: {$description}
- Catégorie: {$categoryFr}
- État: {$conditionFr}
- Mots-clés: {$keywordsStr}

Fourchette de prix typique pour cette catégorie:
- Minimum: {$basePriceRange['min']}€
- Maximum: {$basePriceRange['max']}€
- Moyenne: {$basePriceRange['avg']}€

Ta mission:
1. Analyser la valeur de l'article
2. Considérer l'état, la rareté, la demande
3. Proposer une fourchette de prix RÉALISTE
4. Suggérer un prix de départ idéal
5. Donner des conseils de négociation

Critères d'évaluation:
- Matériaux nobles/qualité: +10-30%
- État excellent: plein prix
- Marque reconnue: +20-50%
- Vintage/unique: +15-40%
- Demande forte: +10-20%
- Aspect écologique: valeur ajoutée

Format de réponse EXACT:
PRICE_MIN: [montant en euros, nombre entier]
PRICE_MAX: [montant en euros, nombre entier]
RECOMMENDED_PRICE: [prix idéal de départ, nombre entier]
NEGOTIATION_MIN: [prix minimum acceptable, nombre entier]
CONFIDENCE: [0-100]
FACTORS: [facteur1: +X%, facteur2: +Y%, facteur3: -Z%]
MARKET_DEMAND: [faible/moyenne/forte]
REASONING: [explication courte en 2-3 phrases]
TIPS: [conseil de vente en 1 phrase]

Règles importantes:
- Sois réaliste pour le marché de l'occasion
- PRICE_MIN doit être >= {$basePriceRange['min']}
- PRICE_MAX doit être <= {$basePriceRange['max']}
- RECOMMENDED_PRICE entre MIN et MAX
- Tiens compte de l'état pour ajuster le prix
- Les prix doivent être des nombres entiers
- Pense marché de seconde main, pas neuf

Génère maintenant ton analyse:
PROMPT;
    }

    /**
     * Parse the AI response into structured data
     */
    protected function parseResponse(string $text, array $basePriceRange, string $condition): array
    {
        $data = [
            'price_min' => $basePriceRange['min'],
            'price_max' => $basePriceRange['max'],
            'recommended_price' => $basePriceRange['avg'],
            'negotiation_min' => 0,
            'confidence' => 0,
            'factors' => [],
            'market_demand' => 'moyenne',
            'reasoning' => '',
            'tips' => ''
        ];

        // Extract prices
        if (preg_match('/PRICE_MIN:\s*(\d+)/i', $text, $matches)) {
            $data['price_min'] = (int) $matches[1];
        }

        if (preg_match('/PRICE_MAX:\s*(\d+)/i', $text, $matches)) {
            $data['price_max'] = (int) $matches[1];
        }

        if (preg_match('/RECOMMENDED_PRICE:\s*(\d+)/i', $text, $matches)) {
            $data['recommended_price'] = (int) $matches[1];
        }

        if (preg_match('/NEGOTIATION_MIN:\s*(\d+)/i', $text, $matches)) {
            $data['negotiation_min'] = (int) $matches[1];
        }

        // Extract confidence
        if (preg_match('/CONFIDENCE:\s*(\d+)/i', $text, $matches)) {
            $data['confidence'] = (int) $matches[1];
        }

        // Extract factors
        if (preg_match('/FACTORS:\s*(.+?)(?=\n[A-Z_]+:|$)/is', $text, $matches)) {
            $factorsStr = trim($matches[1]);
            // Parse factors like "qualité: +20%, état: -10%"
            preg_match_all('/([^:,]+):\s*([-+]?\d+)%/i', $factorsStr, $factorMatches, PREG_SET_ORDER);
            foreach ($factorMatches as $match) {
                $data['factors'][] = [
                    'name' => trim($match[1]),
                    'impact' => (int) $match[2]
                ];
            }
        }

        // Extract market demand
        if (preg_match('/MARKET_DEMAND:\s*(faible|moyenne|forte)/i', $text, $matches)) {
            $data['market_demand'] = strtolower(trim($matches[1]));
        }

        // Extract reasoning
        if (preg_match('/REASONING:\s*(.+?)(?=\n[A-Z_]+:|$)/is', $text, $matches)) {
            $data['reasoning'] = trim($matches[1]);
        }

        // Extract tips
        if (preg_match('/TIPS:\s*(.+?)$/is', $text, $matches)) {
            $data['tips'] = trim($matches[1]);
        }

        // Validate and adjust prices
        $data = $this->validatePrices($data, $basePriceRange);

        // Add formatted strings
        $data['price_range_formatted'] = $data['price_min'] . '€ - ' . $data['price_max'] . '€';
        $data['recommended_price_formatted'] = $data['recommended_price'] . '€';

        return $data;
    }

    /**
     * Validate and adjust prices to ensure coherence
     */
    protected function validatePrices(array $data, array $basePriceRange): array
    {
        // Ensure min <= recommended <= max
        if ($data['price_min'] > $data['recommended_price']) {
            $data['recommended_price'] = $data['price_min'];
        }

        if ($data['recommended_price'] > $data['price_max']) {
            $data['recommended_price'] = $data['price_max'];
        }

        // Ensure negotiation_min is reasonable
        if ($data['negotiation_min'] == 0 || $data['negotiation_min'] < $data['price_min']) {
            $data['negotiation_min'] = (int) ($data['recommended_price'] * 0.85);
        }

        // Ensure prices are within category limits
        $data['price_min'] = max($basePriceRange['min'], $data['price_min']);
        $data['price_max'] = min($basePriceRange['max'], $data['price_max']);

        return $data;
    }

    /**
     * Calculate basic price without AI (fallback)
     */
    protected function calculateBasicPrice(string $category, string $condition): array
    {
        $basePriceRange = $this->categoryBasePrices[$category] ?? $this->categoryBasePrices['other'];
        $multiplier = $this->conditionMultipliers[$condition] ?? 0.75;

        $avgPrice = (int) ($basePriceRange['avg'] * $multiplier);
        $minPrice = (int) ($avgPrice * 0.7);
        $maxPrice = (int) ($avgPrice * 1.3);

        return [
            'success' => true,
            'data' => [
                'price_min' => max($basePriceRange['min'], $minPrice),
                'price_max' => min($basePriceRange['max'], $maxPrice),
                'recommended_price' => $avgPrice,
                'negotiation_min' => (int) ($avgPrice * 0.85),
                'confidence' => 60,
                'factors' => [],
                'market_demand' => 'moyenne',
                'reasoning' => 'Prix calculé selon la catégorie et l\'état de l\'article.',
                'tips' => 'Ajoutez des photos de qualité pour augmenter vos chances de vente.',
                'price_range_formatted' => $minPrice . '€ - ' . $maxPrice . '€',
                'recommended_price_formatted' => $avgPrice . '€',
                'is_fallback' => true
            ],
            'error' => ''
        ];
    }

    /**
     * Translate category to French
     */
    protected function translateCategory(string $category): string
    {
        $translations = [
            'furniture' => 'Mobilier',
            'electronics' => 'Électronique',
            'clothing' => 'Vêtements',
            'books' => 'Livres',
            'toys' => 'Jouets',
            'tools' => 'Outils',
            'decorative' => 'Décoration',
            'appliances' => 'Électroménager',
            'other' => 'Autre'
        ];

        return $translations[$category] ?? $category;
    }

    /**
     * Translate condition to French
     */
    protected function translateCondition(string $condition): string
    {
        $translations = [
            'excellent' => 'Excellent état (comme neuf)',
            'good' => 'Bon état (légères traces d\'usage)',
            'fair' => 'État correct (usure visible)',
            'needs_repair' => 'À réparer (nécessite restauration)'
        ];

        return $translations[$condition] ?? $condition;
    }

    /**
     * Get market demand emoji
     */
    public function getMarketDemandEmoji(string $demand): string
    {
        $emojis = [
            'faible' => '📉',
            'moyenne' => '📊',
            'forte' => '📈'
        ];

        return $emojis[$demand] ?? '📊';
    }
}
