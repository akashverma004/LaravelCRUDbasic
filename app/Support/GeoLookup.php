<?php

namespace App\Support;

final class GeoLookup
{
    public static function normalizeCountryCode(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $countries = config('geo.countries', []);
        $needle = strtoupper(trim($value));

        if (isset($countries[$needle])) {
            return $needle;
        }

        foreach ($countries as $code => $name) {
            if (strtoupper($name) === $needle) {
                return $code;
            }
        }

        return $needle;
    }

    public static function normalizeIndianStateCode(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $states = config('geo.states_in', []);
        $needle = strtoupper(trim($value));

        if (isset($states[$needle])) {
            return $needle;
        }

        foreach ($states as $code => $name) {
            if (strtoupper($name) === $needle) {
                return $code;
            }
        }

        return $needle;
    }

    private function __construct()
    {
    }
}

