<?php

namespace Tests\Unit\Services;

use App\Services\CategoryDetectionService;
use App\Services\GeminiService;
use Tests\TestCase;
use Mockery;

class CategoryDetectionServiceTest extends TestCase
{
    protected CategoryDetectionService $service;
    protected $geminiMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->geminiMock = Mockery::mock(GeminiService::class);
        $this->service = new CategoryDetectionService($this->geminiMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_error_for_empty_description()
    {
        $result = $this->service->detectCategory('');

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['error']);
        $this->assertEmpty($result['data']);
    }

    /** @test */
    public function it_detects_category_successfully()
    {
        $aiResponse = <<<RESPONSE
CATEGORY: furniture
TITLE: Belle table en bois vintage
CONDITION: good
KEYWORDS: table, bois, vintage, salle à manger
CONFIDENCE: 85
REASONING: La description mentionne clairement une table, ce qui correspond à la catégorie mobilier.
RESPONSE;

        $this->geminiMock
            ->shouldReceive('generateContent')
            ->once()
            ->andReturn([
                'success' => true,
                'text' => $aiResponse,
                'error' => ''
            ]);

        $result = $this->service->detectCategory('Une table en bois ancienne');

        $this->assertTrue($result['success']);
        $this->assertEquals('furniture', $result['data']['category']);
        $this->assertEquals('Belle table en bois vintage', $result['data']['title']);
        $this->assertEquals('good', $result['data']['condition']);
        $this->assertEquals(85, $result['data']['confidence']);
        $this->assertCount(4, $result['data']['keywords']);
    }

    /** @test */
    public function it_handles_gemini_api_failure()
    {
        $this->geminiMock
            ->shouldReceive('generateContent')
            ->once()
            ->andReturn([
                'success' => false,
                'text' => '',
                'error' => 'API Error'
            ]);

        $result = $this->service->detectCategory('Un téléphone portable');

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['error']);
    }

    /** @test */
    public function it_defaults_to_other_category_for_invalid_category()
    {
        $aiResponse = <<<RESPONSE
CATEGORY: invalid_category
TITLE: Article inconnu
CONDITION: good
KEYWORDS: article
CONFIDENCE: 50
REASONING: Catégorie non reconnue
RESPONSE;

        $this->geminiMock
            ->shouldReceive('generateContent')
            ->once()
            ->andReturn([
                'success' => true,
                'text' => $aiResponse,
                'error' => ''
            ]);

        $result = $this->service->detectCategory('Quelque chose');

        $this->assertTrue($result['success']);
        $this->assertEquals('other', $result['data']['category']);
    }

    /** @test */
    public function it_validates_categories_correctly()
    {
        $this->assertTrue($this->service->isValidCategory('furniture'));
        $this->assertTrue($this->service->isValidCategory('electronics'));
        $this->assertFalse($this->service->isValidCategory('invalid'));
        $this->assertFalse($this->service->isValidCategory(''));
    }

    /** @test */
    public function it_validates_conditions_correctly()
    {
        $this->assertTrue($this->service->isValidCondition('excellent'));
        $this->assertTrue($this->service->isValidCondition('good'));
        $this->assertTrue($this->service->isValidCondition('needs_repair'));
        $this->assertFalse($this->service->isValidCondition('invalid'));
    }

    /** @test */
    public function it_gets_all_categories()
    {
        $categories = $this->service->getCategories();

        $this->assertIsArray($categories);
        $this->assertArrayHasKey('furniture', $categories);
        $this->assertArrayHasKey('electronics', $categories);
        $this->assertArrayHasKey('clothing', $categories);
    }

    /** @test */
    public function it_gets_all_conditions()
    {
        $conditions = $this->service->getConditions();

        $this->assertIsArray($conditions);
        $this->assertArrayHasKey('excellent', $conditions);
        $this->assertArrayHasKey('good', $conditions);
        $this->assertArrayHasKey('needs_repair', $conditions);
    }

    /** @test */
    public function it_parses_response_with_missing_fields()
    {
        $aiResponse = <<<RESPONSE
CATEGORY: electronics
TITLE: Smartphone
RESPONSE;

        $this->geminiMock
            ->shouldReceive('generateContent')
            ->once()
            ->andReturn([
                'success' => true,
                'text' => $aiResponse,
                'error' => ''
            ]);

        $result = $this->service->detectCategory('Un téléphone');

        $this->assertTrue($result['success']);
        $this->assertEquals('electronics', $result['data']['category']);
        $this->assertEquals('Smartphone', $result['data']['title']);
        $this->assertEquals('good', $result['data']['condition']); // Default
        $this->assertEquals(0, $result['data']['confidence']); // Default
    }

    /** @test */
    public function it_includes_category_and_condition_labels()
    {
        $aiResponse = <<<RESPONSE
CATEGORY: books
TITLE: Roman de science-fiction
CONDITION: excellent
KEYWORDS: livre, roman, science-fiction
CONFIDENCE: 90
REASONING: C'est clairement un livre
RESPONSE;

        $this->geminiMock
            ->shouldReceive('generateContent')
            ->once()
            ->andReturn([
                'success' => true,
                'text' => $aiResponse,
                'error' => ''
            ]);

        $result = $this->service->detectCategory('Un roman de SF');

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('category_label', $result['data']);
        $this->assertArrayHasKey('condition_label', $result['data']);
        $this->assertStringContainsString('Livres', $result['data']['category_label']);
    }

    /** @test */
    public function it_handles_keywords_correctly()
    {
        $aiResponse = <<<RESPONSE
CATEGORY: tools
TITLE: Perceuse électrique
CONDITION: good
KEYWORDS: perceuse, outil, bricolage, électrique
CONFIDENCE: 88
REASONING: Outil de bricolage
RESPONSE;

        $this->geminiMock
            ->shouldReceive('generateContent')
            ->once()
            ->andReturn([
                'success' => true,
                'text' => $aiResponse,
                'error' => ''
            ]);

        $result = $this->service->detectCategory('Perceuse pour bricolage');

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']['keywords']);
        $this->assertContains('perceuse', $result['data']['keywords']);
        $this->assertContains('outil', $result['data']['keywords']);
    }
}
