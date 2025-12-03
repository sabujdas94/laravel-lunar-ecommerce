<?php

namespace App\Pricing;

use Illuminate\Support\Facades\App as AppFacade;
use Lunar\Models\Currency;
use Lunar\Pricing\PriceFormatterInterface;
use NumberFormatter;

class CustomPriceFormatter implements PriceFormatterInterface
{
    /**
     * Mapping of currency codes to preferred symbols.
     */
    protected static array $symbolMap = [
        'USD' => '$',
        'BDT' => '৳',
    ];

    public function __construct(
        public int $value,
        public ?Currency $currency = null,
        public int $unitQty = 1
    ) {
        if (! $this->currency) {
            $this->currency = Currency::getDefault();
        }
    }

    public function decimal(bool $rounding = true): float
    {
        $convertedValue = $this->value / $this->currency->factor;

        return $rounding ? round($convertedValue, $this->currency->decimal_places) : $convertedValue;
    }

    public function unitDecimal(bool $rounding = true): float
    {
        $convertedValue = $this->value / $this->currency->factor / $this->unitQty;

        return $rounding ? round($convertedValue, $this->currency->decimal_places) : $convertedValue;
    }

    public function formatted(?string $locale = null, string $formatterStyle = NumberFormatter::CURRENCY, ?int $decimalPlaces = null, bool $trimTrailingZeros = true, bool $forceSymbolFirst = false): mixed
    {
        return $this->formatValue($this->decimal(false), $locale, $formatterStyle, $decimalPlaces, $trimTrailingZeros, $forceSymbolFirst);
    }

    public function unitFormatted(?string $locale = null, string $formatterStyle = NumberFormatter::CURRENCY, ?int $decimalPlaces = null, bool $trimTrailingZeros = true, bool $forceSymbolFirst = false): mixed
    {
        return $this->formatValue($this->unitDecimal(false), $locale, $formatterStyle, $decimalPlaces, $trimTrailingZeros, $forceSymbolFirst);
    }

    protected function formatValue(int|float $value, ?string $locale = null, string $formatterStyle = NumberFormatter::CURRENCY, ?int $decimalPlaces = null, bool $trimTrailingZeros = true, bool $forceSymbolFirst = false): mixed
    {
        if (! $locale) {
            $locale = AppFacade::currentLocale();
        }

        // Use NumberFormatter pattern and formatCurrency to control decimals and position.
        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Use NumberFormatter pattern and formatCurrency to control decimals and position.
        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Build pattern: currency symbol then grouping; allow decimal places if requested
        $fractionDigits = $decimalPlaces ?? 0;
        $pattern = '¤#,##0' . ($fractionDigits > 0 ? '.' . str_repeat('0', $fractionDigits) : '');

        $fmt->setPattern($pattern);

        // Format using major units (value already expected in major units)
        $formatted = $fmt->formatCurrency($value, $this->currency->code);

        // Determine symbol: prefer our mapping, otherwise use Intl symbol
        $symbol = static::$symbolMap[$this->currency->code] ?? $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        // If Intl returned the currency code itself or generic sign, prefer mapping when available
        if ($symbol === $this->currency->code || $symbol === '¤') {
            $symbol = static::$symbolMap[$this->currency->code] ?? $symbol;
        }

        if ($symbol) {
            $formatted = str_replace($this->currency->code, $symbol, $formatted);
        }

        // Remove any non-breaking space characters
        $formatted = str_replace("\u{00A0}", '', $formatted);

        // Force symbol-first output if requested
        if ($forceSymbolFirst) {
            // Extract numeric part (digits, grouping separators, decimal separator, minus)
            $numberOnly = preg_replace('/[^0-9.,\-]/', '', $formatted);
            $numberOnly = trim($numberOnly);
            $formatted = ($symbol ?? '') . ' ' . $numberOnly;
        }

        return $formatted;
    }

    
}
