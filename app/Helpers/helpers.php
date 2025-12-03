<?php

use Lunar\DataTypes\Price as LunarPrice;

if (! function_exists('format_price')) {
    /**
     * Format a price into a localized currency string.
     *
     * Accepts:
     * - \Lunar\DataTypes\Price instances (preferred)
     * - integer value in minor units (e.g., cents)
     * - numeric values
     *
     * Signature mirrors DefaultPriceFormatter::formatted
     *
     * @param mixed $price
     * @param mixed $currency Optional currency model or currency code (string)
     * @param string|null $locale
     * @param string $formatterStyle One of NumberFormatter::* constants
     * @param int|null $decimalPlaces
     * @param bool $trimTrailingZeros
     * @return mixed
     */
    function format_price(
        $price,
        $currency = null,
        ?string $locale = null,
        string $formatterStyle = \NumberFormatter::CURRENCY,
        ?int $decimalPlaces = null,
        bool $trimTrailingZeros = true
    ): mixed {
        if ($price instanceof LunarPrice) {
            return $price->formatted($locale, $formatterStyle, $decimalPlaces, $trimTrailingZeros);
        }

        if (is_int($price) || is_numeric($price)) {
            // Resolve currency model
            if ($currency instanceof \Lunar\Models\Contracts\Currency) {
                $curr = $currency;
            } elseif (is_string($currency)) {
                $curr = \Lunar\Models\Currency::where('code', $currency)->first() ?? \Lunar\Models\Currency::getDefault();
            } else {
                $curr = \Lunar\Models\Currency::getDefault();
            }

            $formatter = new \Lunar\Pricing\DefaultPriceFormatter((int) $price, $curr);

            return $formatter->formatted($locale, $formatterStyle, $decimalPlaces, $trimTrailingZeros);
        }

        return (string) $price;
    }
}

if (! function_exists('format_price_simple')) {
    /**
     * Simple formatter: always uses western digits, allows forcing symbol-first and decimal places.
     *
     * @param mixed $price Lunar\DataTypes\Price or int/minor units
     * @param mixed $currency Currency model or currency code (optional if $price is a Price)
     * @param string|null $locale
     * @param int $decimalPlaces
     * @param bool $symbolFirst
     * @param bool $useWesternDigits
     * @return string
     */
    function format_price_simple(
        $price,
        $currency = null,
        ?string $locale = 'en',
        int $decimalPlaces = 0,
        bool $symbolFirst = true,
        bool $useWesternDigits = true
    ): string {
        // Resolve Lunar\DataTypes\Price
        if ($price instanceof LunarPrice) {
            $priceData = $price;
            $currencyModel = $priceData->currency;
            // main units (float)
            $amount = $priceData->decimal(false);
        } else {
            // numeric minor units
            if (is_int($price) || is_numeric($price)) {
                if ($currency instanceof \Lunar\Models\Contracts\Currency) {
                    $currencyModel = $currency;
                } elseif (is_string($currency)) {
                    $currencyModel = \Lunar\Models\Currency::where('code', $currency)->first() ?? \Lunar\Models\Currency::getDefault();
                } else {
                    $currencyModel = \Lunar\Models\Currency::getDefault();
                }

                $amount = (float) $price / (int) $currencyModel->factor;
            } else {
                return (string) $price;
            }
        }

        // Round to requested decimal places
        $amountRounded = $decimalPlaces >= 0 ? round($amount, $decimalPlaces) : $amount;

        // Format number using western digits (NumberFormatter DECIMAL)
        $numberFormatter = new \NumberFormatter($locale ?? 'en', \NumberFormatter::DECIMAL);
        $numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $decimalPlaces);
        $formattedNumber = $numberFormatter->format($amountRounded);

        // Get currency symbol for the currency code
        $currencyFormatter = new \NumberFormatter($locale ?? 'en', \NumberFormatter::CURRENCY);
        $currencyFormatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, $currencyModel->code);
        $symbol = $currencyFormatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);

        if ($symbolFirst) {
            return ($symbol . ($useWesternDigits ? ' ' : '')) . $formattedNumber;
        }

        return $formattedNumber . ($useWesternDigits ? ' ' : '') . $symbol;
    }
}
