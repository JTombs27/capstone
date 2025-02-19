<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Farms', '192.1k'),
            Stat::make('Manok', '21%'),
            Stat::make('Baboy', '3:12'),
            // Stat::make('Baboy2', '3:12'),
        ];
    }
}
