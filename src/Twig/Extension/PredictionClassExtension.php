<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\PredictionClassRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PredictionClassExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('predictionClass', [PredictionClassRuntime::class, 'getPredictionClass']),
        ];
    }
}
