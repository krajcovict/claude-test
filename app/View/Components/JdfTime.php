<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Renders one Zasspoje time value (a "0406"-style 4-digit string, or one of the
 * JDF literals "|" / "<") as a readable HH:MM label plus a variant for styling.
 */
class JdfTime extends Component
{
    public string $label;
    public string $variant;

    public function __construct(?string $value = null)
    {
        [$this->label, $this->variant] = $this->resolve($value);
    }

    /** @return array{0: string, 1: string} */
    private function resolve(?string $value): array
    {
        if ($value === null || $value === '') {
            return ['—', 'empty'];
        }

        if ($value === '|') {
            return ['prechádza', 'through'];
        }

        if ($value === '<') {
            return ['inou trasou', 'reroute'];
        }

        if (preg_match('/^\d{3,4}$/', $value)) {
            $padded = str_pad($value, 4, '0', STR_PAD_LEFT);

            return [substr($padded, 0, 2) . ':' . substr($padded, 2, 2), 'time'];
        }

        return [$value, 'time'];
    }

    public function render(): View
    {
        return view('components.jdf-time');
    }
}
