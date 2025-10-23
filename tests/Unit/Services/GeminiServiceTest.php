<?php

namespace Tests\Unit\Services;

use App\Services\GeminiService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    protected GeminiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('services.gemini.api_key', 'test-api-key');
        Config::set('services.gemini.api_url', 'https://api.test.com');
        
        $this->service = new GeminiService();
    }

    /** @test */
    public function it_generates_event_description_successfully()
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'This is a great workshop about recycling.']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->generateEventDescription(
            'Recycling Workshop',
            'workshop',
            'Community Center'
        );

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['description']);
        $this->assertStringContainsString('recycling', strtolower($result['description']));
    }

    /** @test */
    public function it_handles_api_failure_for_description()
    {
        Http::fake([
            '*' => Http::response([], 500)
        ]);

        $result = $this->service->generateEventDescription(
            'Test Event',
            'workshop'
        );

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertNotEmpty($result['error']);
    }

    /** @test */
    public function it_generates_event_faq_successfully()
    {
        $faqResponse = <<<FAQ
Q: What should I bring?
A: Please bring your enthusiasm and a notebook.

Q: Is there a fee?
A: No, this event is completely free.

Q: What is the duration?
A: The event will last approximately 2 hours.
FAQ;

        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $faqResponse]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->generateEventFAQ(
            'Workshop',
            'workshop',
            'A great workshop'
        );

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('faqs', $result);
        $this->assertIsArray($result['faqs']);
        $this->assertGreaterThan(0, count($result['faqs']));
        
        foreach ($result['faqs'] as $faq) {
            $this->assertArrayHasKey('question', $faq);
            $this->assertArrayHasKey('answer', $faq);
        }
    }

    /** @test */
    public function it_handles_api_failure_for_faq()
    {
        Http::fake([
            '*' => Http::response([], 500)
        ]);

        $result = $this->service->generateEventFAQ(
            'Test Event',
            'workshop',
            'Description'
        );

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    /** @test */
    public function it_generates_generic_content_successfully()
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Generated content response']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->generateContent('Test prompt');

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['text']);
        $this->assertEquals('Generated content response', $result['text']);
        $this->assertEmpty($result['error']);
    }

    /** @test */
    public function it_handles_invalid_api_response_structure()
    {
        Http::fake([
            '*' => Http::response([
                'invalid' => 'structure'
            ], 200)
        ]);

        $result = $this->service->generateContent('Test prompt');

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['error']);
    }

    /** @test */
    public function it_parses_faq_response_correctly()
    {
        $faqText = <<<FAQ
Q: Question one?
A: Answer one.

Q: Question two?
A: Answer two.

Q: Question three?
A: Answer three.
FAQ;

        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $faqText]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->generateEventFAQ('Title', 'type', 'description');

        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['faqs']);
        $this->assertEquals('Question one?', $result['faqs'][0]['question']);
        $this->assertEquals('Answer one.', $result['faqs'][0]['answer']);
    }

    /** @test */
    public function it_builds_description_prompt_with_location()
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Description']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->generateEventDescription(
            'Test Event',
            'workshop',
            'Test Location'
        );

        $this->assertTrue($result['success']);
    }

    /** @test */
    public function it_builds_description_prompt_without_location()
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Description without location']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->generateEventDescription(
            'Test Event',
            'conference'
        );

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['description']);
    }
}
