<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class PredictionClassRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function getPredictionClass($points): string
    {
        return match ($points) {
            5 => 'table-danger',
            3 => 'table-info',
            default => ''
        };
    }
}
