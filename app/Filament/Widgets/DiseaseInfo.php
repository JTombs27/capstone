<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DiseaseInfo extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 2;
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
