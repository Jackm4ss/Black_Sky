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

        $options = [];

        foreach (self::assignedCountries() as $country) {
            $code = $country['alpha2'] ?? null;
            $name = $country['name'] ?? null;

            if (! is_string($code) || ! is_string($name)) {
                continue;
            }

            $options[$code] = $name;
        }

        uasort($options, static fn (string $first, string $second): int => $first <=> $second);

        return $options;
    }

    public static function callingCodeFor(?string $code): ?string
    {
        $normalizedCode = strtoupper(trim((string) $code));

        if ($normalizedCode === '') {
            return null;
        }

        foreach (self::assignedCountries() as $country) {
            if (($country['alpha2'] ?? null) !== $normalizedCode) {
                continue;
            }

            $callingCodes = $country['countryCallingCodes'] ?? [];

            if (! is_array($callingCodes) || ! is_string($callingCodes[0] ?? null)) {
                return null;
            }

            return preg_replace('/\s+/', '', $callingCodes[0]) ?: null;
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function assignedCountries(): array
    {
        static $countries = null;

        if ($countries !== null) {
            return $countries;
        }

        $path = resource_path('data/country-data-list-countries.json');

        if (! is_file($path)) {
            return $countries = [];
        }

        try {
            $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $countries = [];
        }

        if (! is_array($decoded)) {
            return $countries = [];
        }

        $countries = [];

        foreach ($decoded as $country) {
            if (! is_array($country)) {
                continue;
            }

            $code = $country['alpha2'] ?? null;
            $name = $country['name'] ?? null;

            if (! is_string($code) || ! preg_match('/^[A-Z]{2}$/', $code) || ! is_string($name) || $name === '') {
                continue;
            }

            $country['alpha2'] = $code;
            $country['name'] = $name;
            $countries[] = $country;
        }

        return $countries;
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
