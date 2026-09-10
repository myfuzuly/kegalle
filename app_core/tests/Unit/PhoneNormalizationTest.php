<?php

namespace Tests\Unit;

use App\Http\Controllers\Dashboard\StoreController;
use PHPUnit\Framework\TestCase;

class PhoneNormalizationTest extends TestCase
{
    public function test_normalizes_local_phone_to_international(): void
    {
        $result = StoreController::normalizePhones(['phone' => '0712345678']);
        $this->assertEquals('+94712345678', $result['phone']);
    }

    public function test_normalizes_whatsapp_without_plus(): void
    {
        $result = StoreController::normalizePhones(['whatsapp' => '0712345678']);
        $this->assertEquals('94712345678', $result['whatsapp']);
    }

    public function test_already_international_phone_unchanged(): void
    {
        $result = StoreController::normalizePhones(['phone' => '+94712345678']);
        $this->assertEquals('+94712345678', $result['phone']);
    }

    public function test_empty_phone_stays_empty(): void
    {
        $result = StoreController::normalizePhones(['phone' => '']);
        $this->assertEquals('', $result['phone']);
    }

    public function test_normalizes_phone_with_spaces(): void
    {
        $result = StoreController::normalizePhones(['phone' => '071 234 5678']);
        $this->assertEquals('+94712345678', $result['phone']);
    }
}
