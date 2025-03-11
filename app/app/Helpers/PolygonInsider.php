<?php

namespace App\Helpers;

class PolygonInsider
{
    public static function inside(float $lat, float $lon, iterable $polygons): ?int
    {
        $point = [$lat, $lon];
        $inside = false;

        foreach ($polygons as $index => $polygon) {
            $inside = false;

            $slice = $polygon;
            $countPoints = count($slice);

            $y = floatval($point[1]);
            $x = floatval($point[0]);
            $j = $countPoints - 1;

            for ($i = 0; $i < $countPoints; $j = $i++) {
                $jY = floatval($slice[$j][0]);
                $iY = floatval($slice[$i][0]);

                if (
                    (($iY <= $y && $y < $jY) || ($jY <= $y && $y < $iY)) &&
                    ($x < ($slice[$j][1] - $slice[$i][1]) * ($y - $iY) / ($jY - $iY) + $slice[$i][1])
                ) {
                    $inside = !$inside;
                }
            }

            if ($inside) return $index;
        }

        return null;
    }
}
