<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_strips_script_tags(): void
    {
        $input  = '<p>Hello</p><script>alert("xss")</script>';
        $output = HtmlSanitizer::clean($input);
        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringNotContainsString('alert', $output);
        $this->assertStringContainsString('Hello', $output);
    }

    public function test_strips_on_event_attributes(): void
    {
        $input  = '<img src="cat.jpg" onerror="steal()">';
        $output = HtmlSanitizer::clean($input);
        $this->assertStringNotContainsString('onerror', $output);
        $this->assertStringContainsString('cat.jpg', $output);
    }

    public function test_strips_javascript_href(): void
    {
        $input  = '<a href="javascript:void(0)">Click me</a>';
        $output = HtmlSanitizer::clean($input);
        $this->assertStringNotContainsString('javascript:', $output);
        $this->assertStringContainsString('Click me', $output);
    }

    public function test_strips_iframe(): void
    {
        $input  = '<p>Text</p><iframe src="https://evil.com"></iframe>';
        $output = HtmlSanitizer::clean($input);
        $this->assertStringNotContainsString('<iframe', $output);
        $this->assertStringContainsString('Text', $output);
    }

    public function test_preserves_safe_html(): void
    {
        $input  = '<p>Buy a <strong>used Toyota</strong> for LKR 500,000.</p>';
        $output = HtmlSanitizer::clean($input);
        $this->assertStringContainsString('<strong>', $output);
        $this->assertStringContainsString('500,000', $output);
    }

    public function test_null_input_returns_null(): void
    {
        $this->assertNull(HtmlSanitizer::clean(null));
    }

    public function test_empty_string_returns_empty(): void
    {
        $this->assertSame('', HtmlSanitizer::clean(''));
    }
}
