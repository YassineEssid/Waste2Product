<?php

namespace App\Services;

use App\Models\WasteItem;
use Illuminate\Support\Facades\Log;

/**
 * Service for generating creative transformation ideas using Gemini AI
 */
class TransformationIdeasService
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Generate transformation ideas for a waste item
     *
     * @param WasteItem $wasteItem
     * @param int $count Number of ideas to generate (default: 5)
     * @return array
     */
    public function generateIdeas(WasteItem $wasteItem, int $count = 5): array
    {
        try {
            $prompt = $this->buildPrompt($wasteItem, $count);

            Log::info('Generating transformation ideas for waste item', [
                'waste_item_id' => $wasteItem->id,
                'title' => $wasteItem->title,
                'category' => $wasteItem->category,
            ]);

            $response = $this->gemini->generateContent($prompt);

            Log::info('Gemini API Response received', [
                'success' => $response['success'],
                'has_text' => !empty($response['text']),
                'error' => $response['error'] ?? 'none'
            ]);

            // Check if API call was successful
            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'API call failed');
            }

            // Parse the response
            $ideas = $this->parseResponse($response['text']);

            Log::info('Successfully generated transformation ideas', [
                'waste_item_id' => $wasteItem->id,
                'ideas_count' => count($ideas),
            ]);

            return [
                'success' => true,
                'ideas' => $ideas,
                'waste_item' => [
                    'id' => $wasteItem->id,
                    'title' => $wasteItem->title,
                    'category' => $wasteItem->category,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Transformation Ideas Generation Error', [
                'waste_item_id' => $wasteItem->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de générer des idées pour le moment.',
                'ideas' => []
            ];
        }
    }

    /**
     * Build the prompt for Gemini API
     *
     * @param WasteItem $wasteItem
     * @param int $count
     * @return string
     */
    protected function buildPrompt(WasteItem $wasteItem, int $count): string
    {
        $categoryInfo = $this->getCategoryContext($wasteItem->category);

        return <<<PROMPT
Tu es un expert en upcycling et transformation créative de déchets. Tu dois proposer {$count} idées de produits innovants et écologiques à créer à partir du déchet suivant :

**Déchet à transformer :**
- Titre : {$wasteItem->title}
- Description : {$wasteItem->description}
- Catégorie : {$wasteItem->category}
- Quantité : {$wasteItem->quantity}
- État : {$wasteItem->condition}

{$categoryInfo}

**Instructions :**
1. Propose exactement {$count} idées de transformation créatives et réalistes
2. Chaque idée doit être unique et innovante
3. Les idées doivent être adaptées aux compétences d'un artisan
4. Privilégie les transformations qui ajoutent de la valeur
5. Pense à l'aspect écologique et durable

**Format de réponse STRICTEMENT JSON :**
```json
{"ideas": [{"title": "Nom du produit transforme", "description": "Description detaillee", "difficulty": "facile|moyen|difficile", "estimated_time_hours": 4, "materials_needed": ["materiel 1", "materiel 2"], "selling_price_range": "XX-XX euros", "eco_impact": "Impact ecologique", "target_audience": "Public cible"}]}
```

IMPORTANT:
- N'utilise PAS d'accents dans le JSON (é -> e, è -> e, à -> a, etc.)
- Retourne le JSON sur UNE SEULE ligne, sans retours a la ligne ni indentation
- Utilise uniquement des caracteres ASCII simples
Reponds UNIQUEMENT avec le JSON, sans texte supplementaire ni code blocks.
PROMPT;
    }

    /**
     * Get category-specific context
     *
     * @param string|null $category
     * @return string
     */
    protected function getCategoryContext(?string $category): string
    {
        $contexts = [
            'electronics' => "**Contexte :** Électronique - Pense à la récupération de composants, circuits imprimés, boîtiers, ou création d'objets décoratifs lumineux.",
            'furniture' => "**Contexte :** Mobilier - Pense à la restauration, modernisation, customisation, ou transformation en nouveaux meubles.",
            'clothing' => "**Contexte :** Vêtements - Pense à l'upcycling textile, patchwork, accessoires de mode, ou objets de décoration.",
            'plastic' => "**Contexte :** Plastique - Pense aux pots de plantes, organisateurs, objets décoratifs, ou contenants réutilisables.",
            'metal' => "**Contexte :** Métal - Pense aux sculptures, lampes industrielles, porte-manteaux, ou objets vintage.",
            'glass' => "**Contexte :** Verre - Pense aux vases, luminaires, terrariums, ou objets décoratifs.",
            'wood' => "**Contexte :** Bois - Pense aux étagères, cadres, petits meubles, ou objets artisanaux.",
            'paper' => "**Contexte :** Papier - Pense au papier recyclé, carnets, origami artistique, ou objets décoratifs.",
            'other' => "**Contexte :** Matériau divers - Sois créatif et propose des idées originales adaptées au matériau."
        ];

        return $contexts[$category] ?? $contexts['other'];
    }

    /**
     * Parse Gemini API response
     *
     * @param string $response
     * @return array
     */
    protected function parseResponse(string $response): array
    {
        try {
            // Clean the response - remove markdown code blocks if present
            $cleanResponse = preg_replace('/```json\s*|\s*```/', '', $response);
            $cleanResponse = trim($cleanResponse);

            // Fix common encoding issues
            $cleanResponse = mb_convert_encoding($cleanResponse, 'UTF-8', 'UTF-8');

            // Remove ALL control characters including newlines and tabs
            $cleanResponse = str_replace(["\n", "\r", "\t"], ' ', $cleanResponse);
            $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse); // Collapse multiple spaces

            // Decode JSON
            $data = json_decode($cleanResponse, true, 512, JSON_INVALID_UTF8_IGNORE);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('JSON decode error', [
                    'error' => json_last_error_msg(),
                    'response' => substr($cleanResponse, 0, 500)
                ]);
                return $this->getFallbackIdeas();
            }

            if (!isset($data['ideas']) || !is_array($data['ideas'])) {
                Log::warning('Invalid response structure', [
                    'has_ideas_key' => isset($data['ideas']),
                    'data_keys' => array_keys($data ?? [])
                ]);
                return $this->getFallbackIdeas();
            }

            // Validate and clean each idea
            $validatedIdeas = [];
            foreach ($data['ideas'] as $idea) {
                if ($this->validateIdea($idea)) {
                    $validatedIdeas[] = [
                        'title' => $this->cleanText($idea['title']),
                        'description' => $this->cleanText($idea['description']),
                        'difficulty' => $idea['difficulty'] ?? 'moyen',
                        'estimated_time_hours' => (int)($idea['estimated_time_hours'] ?? 4),
                        'materials_needed' => array_map([$this, 'cleanText'], $idea['materials_needed'] ?? []),
                        'selling_price_range' => $this->cleanText($idea['selling_price_range'] ?? 'À déterminer'),
                        'eco_impact' => $this->cleanText($idea['eco_impact'] ?? 'Réduction des déchets'),
                        'target_audience' => $this->cleanText($idea['target_audience'] ?? 'Grand public')
                    ];
                }
            }

            return !empty($validatedIdeas) ? $validatedIdeas : $this->getFallbackIdeas();

        } catch (\Exception $e) {
            Log::error('Response parsing error', [
                'error' => $e->getMessage(),
                'response' => substr($response, 0, 200)
            ]);
            return $this->getFallbackIdeas();
        }
    }

    /**
     * Clean text from encoding issues
     *
     * @param string $text
     * @return string
     */
    protected function cleanText(string $text): string
    {
        // Convert to UTF-8 and remove invalid characters
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        // Remove any remaining weird characters
        $text = preg_replace('/[^\p{L}\p{N}\p{P}\p{Z}\p{M}\s]/u', '', $text);
        return trim($text);
    }

    /**
     * Validate idea structure
     *
     * @param mixed $idea
     * @return bool
     */
    protected function validateIdea($idea): bool
    {
        return is_array($idea)
            && isset($idea['title'])
            && isset($idea['description'])
            && !empty($idea['title'])
            && !empty($idea['description']);
    }

    /**
     * Get fallback ideas when AI generation fails
     *
     * @return array
     */
    protected function getFallbackIdeas(): array
    {
        return [
            [
                'title' => 'Objet Décoratif Personnalisé',
                'description' => 'Transformer ce matériau en un objet de décoration unique pour la maison ou le jardin.',
                'difficulty' => 'facile',
                'estimated_time_hours' => 3,
                'materials_needed' => ['Peinture', 'Colle', 'Outils de base'],
                'selling_price_range' => '15-30 €',
                'eco_impact' => 'Réutilisation créative du matériau',
                'target_audience' => 'Amateurs de décoration éco-responsable'
            ],
            [
                'title' => 'Accessoire Fonctionnel',
                'description' => 'Créer un accessoire pratique pour le quotidien en valorisant le matériau existant.',
                'difficulty' => 'moyen',
                'estimated_time_hours' => 5,
                'materials_needed' => ['Outils de découpe', 'Fixations', 'Finitions'],
                'selling_price_range' => '20-40 €',
                'eco_impact' => 'Prolongation du cycle de vie du matériau',
                'target_audience' => 'Consommateurs éco-conscients'
            ],
            [
                'title' => 'Création Artistique',
                'description' => 'Transformer en œuvre d\'art ou sculpture originale mettant en valeur le matériau recyclé.',
                'difficulty' => 'difficile',
                'estimated_time_hours' => 8,
                'materials_needed' => ['Outils spécialisés', 'Matériaux complémentaires', 'Vernis'],
                'selling_price_range' => '50-100 €',
                'eco_impact' => 'Sensibilisation par l\'art au recyclage',
                'target_audience' => 'Collectionneurs et amateurs d\'art recyclé'
            ]
        ];
    }
}
