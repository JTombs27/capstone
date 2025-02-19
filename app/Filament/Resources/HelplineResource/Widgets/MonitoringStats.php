<?php

namespace App\Filament\Resources\HelplineResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonitoringStats extends BaseWidget
{

    protected function getColumns(): int
    {
        // $count = count($this->getCachedStats());

        // if ($count < 3) {
        //     return 3;
        // }

        // if (($count % 3) !== 1) {
        //     return 3;
        // }

        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Daily Helpline Report', '0')
                ->icon('heroicon-m-arrow-trending-up')
                ->description('32k dicrease')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Monitored Diseases', '5 total cases')
                ->description('ASF, Kulira, Bird-flue')
                ->color('success'),
            Stat::make('Cleared Cases', '75%')
                ->description('ASF, Kulira, Bird-flue')
                ->color('success'),
        ];
    }
}
