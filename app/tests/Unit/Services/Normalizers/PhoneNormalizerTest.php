<?php

namespace Tests\Unit\Services\Normalizers;

use App\Services\Normalizers\PhoneNormalizer;
use Tests\TestCase;

class PhoneNormalizerTest extends TestCase
{
    /**
     * @dataProvider phoneNormalizationProvider
     */
    public function test_normalize_phone($input, $expected)
    {
        $this->assertEquals($expected, PhoneNormalizer::normalizePhone($input));
    }

    public static function phoneNormalizationProvider(): array
    {
        return [
            ['79261234567', '9261234567'],
            ['+7 926 123 45 67', '9261234567'],
            ['8(926)123-45-67', '9261234567'],
            ['9261234567', '9261234567'],
            ['7 926 123 45 67', '9261234567'],
            
            ['123', null],
            ['792612345678', null],
            ['not a phone', null],
            ['', null],
            ['+44 20 1234 5678', null],
        ];
    }

    /**
     * @dataProvider phoneHumanizationProvider
     */
    public function test_humanize_phone($input, $expected)
    {
        $this->assertEquals($expected, PhoneNormalizer::humanizePhone($input));
    }

    public static function phoneHumanizationProvider(): array
    {
        return [
            ['79261234567', '+7 (926) 123-45-67'],
            ['+7 926 123 45 67', '+7 (926) 123-45-67'],
            ['8(926)123-45-67', '+7 (926) 123-45-67'],
            ['9261234567', '+7 (926) 123-45-67'],
            
            ['123', null],
            ['not a phone', null],
            ['', null],
        ];
    }
}