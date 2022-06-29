<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('percentage_change', [$this, 'percentageChange'], ['is_safe' => ['html']]),
        ];
    }

    public function percentageChange(float $number): string
    {
        $result = number_format($number, 2, ',', ' ') . '%';

        $rounded_number = round($number, 2);

        if ($rounded_number > 0) {
            $result = '<span class="text-success">+'.$result.'</span>';
        }
        if ($rounded_number < 0) {
            $result = '<span class="text-danger">'.$result.'</span>';
        }

        return $result;
    }
}
