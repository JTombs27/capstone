<?php

namespace App\Filament\Widgets;

use App\Livewire\AnimalHelp;
use App\Models\Helpline;
use App\Services\SmsService;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';
    protected function getColumns(): int
    {
        return 1; // Change this to any number of columns you want
    }
    protected function getStats(): array
    {
        $smsService = new SmsService();
        $balanceResponse = $smsService->getBalance();

        // Ensure the response is an array and contains "credit_balance"
        $balance = is_array($balanceResponse) && isset($balanceResponse["credit_balance"])
            ? $balanceResponse["credit_balance"]
            : "Unavailable";
        $color = "success";
        $icon  = "heroicon-m-arrow-trending-up";
        if ($balance == "Unavailable") {
            $balance = $balance;
            $color = "danger";
            $icon  = "heroicon-m-eye-slash";
        } else  if ($balance < 500) {
            $color = "danger";
            $balance = $balance . " ✉";
            $icon  = "heroicon-m-arrow-trending-down";
        } else {
            $balance = $balance . " ✉";
        }

        $helpLineTotal      = Helpline::count();
        $positivePercent    = Helpline::where("status", "Positive")->orWhere("status", "Monitored")->count();
        $positivePercentC    = ($positivePercent / $helpLineTotal) * 100;
        $formated           = number_format($positivePercentC, 2);
        return [
            Stat::make('SMS Balance', $balance)
                ->description('Notification credit Balance.')
                ->descriptionIcon($icon)
                ->color($color),
            Stat::make('Total Report', $helpLineTotal)
                ->description('Total help-line report.'),
            Stat::make('Positive Sample', '(' . $positivePercent . ') ' . $formated . ' %')
                ->description('Positive disease result.'),
        ];
    }
}
