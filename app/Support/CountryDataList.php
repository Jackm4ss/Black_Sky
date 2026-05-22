<?php

namespace App\Support;

use JsonException;

final class CountryDataList
{
    /**
     * @return array<string, string>
     */
    public static function assignedCountryOptions(): array
    {
        static $options = null;

        if ($options !== null) {
            return $options;
        }

        $path = resource_path('data/country-data-list-countries.json');

        if (! is_file($path)) {
            return $options = [];
        }

        try {
            $countries = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $options = [];
        }

        if (! is_array($countries)) {
            return $options = [];
        }

        $options = [];

        foreach ($countries as $country) {
            if (! is_array($country)) {
                continue;
            }

            $code = $country['alpha2'] ?? null;
            $name = $country['name'] ?? null;

            if (! is_string($code) || ! preg_match('/^[A-Z]{2}$/', $code) || ! is_string($name) || $name === '') {
                continue;
            }

            $options[$code] = $name;
        }

        uasort($options, static fn (string $first, string $second): int => $first <=> $second);

        return $options;
    }

    /**
     * @param  iterable<int, string|null>  $codes
     * @return array<string, string>
     */
    public static function assignedCountryOptionsForCodes(iterable $codes): array
    {
        $allOptions = self::assignedCountryOptions();
        $options = [];

        foreach ($codes as $code) {
            if (! is_string($code)) {
                continue;
            }

            $normalizedCode = strtoupper(trim($code));

            if ($normalizedCode === '' || ! isset($allOptions[$normalizedCode])) {
                continue;
            }

            $options[$normalizedCode] = $allOptions[$normalizedCode];
        }

        uasort($options, static fn (string $first, string $second): int => $first <=> $second);

        return $options;
    }
}
