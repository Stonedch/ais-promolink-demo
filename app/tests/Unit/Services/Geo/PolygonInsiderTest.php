<?php

namespace Tests\Unit\Services\Geo;

use App\Services\Geo\PolygonInsider;
use Tests\TestCase;

class PolygonInsiderTest extends TestCase
{
    /**
     * @dataProvider pointInPolygonProvider
     */
    public function test_inside_polygon($point, $polygons, $expected)
    {
        $result = PolygonInsider::inside($point[0], $point[1], $polygons);
        $this->assertEquals($expected, $result);
    }

    public static function pointInPolygonProvider(): array
    {
        $square = [
            [0, 0],
            [0, 10],
            [10, 10],
            [10, 0],
        ];

        $triangle = [
            [0, 0],
            [5, 10],
            [10, 0],
        ];

        return [
            [
                [5, 5], 
                [$square], 
                0
            ],
            [
                [0, 5], 
                [$square], 
                0
            ],
            [
                [15, 15], 
                [$square], 
                null
            ],
            [
                [5, 5], 
                [$triangle], 
                0
            ],
            [
                [5, 6], 
                [$triangle], 
                null
            ],
            [
                [5, 5], 
                [[[20, 20], [20, 30], [30, 30]], $square], 
                1
            ],
            [
                [5, 5], 
                [], 
                null
            ],
            [
                [0, 0], 
                [$square], 
                0
            ],
        ];
    }

    public function test_edge_cases()
    {
        $line = [[0, 0], [10, 10]];
        $this->assertNull(PolygonInsider::inside(5, 5, [$line]));

        $point = [[5, 5]];
        $this->assertNull(PolygonInsider::inside(5, 5, [$point]));
    }

    /**
     * @dataProvider precisionProvider
     */
    public function test_coordinate_precision($lat, $lon, $expected)
    {
        $square = [
            [0.000001, 0.000001],
            [0.000001, 0.000010],
            [0.000010, 0.000010],
            [0.000010, 0.000001],
        ];

        $result = PolygonInsider::inside($lat, $lon, [$square]);
        $this->assertEquals($expected, $result);
    }

    public static function precisionProvider(): array
    {
        return [
            [0.000005, 0.000005, 0],
            [0.000000, 0.000000, null],
            [0.000001, 0.000001, 0],
        ];
    }

    public function test_large_polygon_set()
    {
        $polygons = [];
        for ($i = 0; $i < 100; $i++) {
            $polygons[] = [
                [$i, $i],
                [$i, $i + 1],
                [$i + 1, $i + 1],
                [$i + 1, $i],
            ];
        }

        $this->assertEquals(49, PolygonInsider::inside(49.5, 49.5, $polygons));

        $this->assertNull(PolygonInsider::inside(-1, -1, $polygons));
    }
}