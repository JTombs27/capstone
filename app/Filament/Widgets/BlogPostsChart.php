<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Arr;
use App\Models\Municipality;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading   = 'Helpline Report Chart';
    protected static ?int $sort         = 2;
    protected function getData(): array
    {
        $municipalities = Municipality::orderBy('municipality_name')->pluck('municipality_name')->toArray();
        $bgColor = Municipality::orderBy('municipality_name')
            ->pluck('color')
            ->map(fn($color) => strtolower($color))
            ->toArray();
        $helpLineCounts = Municipality::leftJoin('helplines', 'municipalities.id', '=', 'helplines.query_municipality')
            ->select(DB::raw('COUNT(helplines.id) as help_line_count'))
            ->groupBy('municipalities.id')
            ->orderBy('municipalities.municipality_name')
            ->pluck('help_line_count')
            ->toArray();
        return [
            'datasets' => [
                [
                    'label' => 'Report Cases',
                    'data' =>  $helpLineCounts,
                    'fill' => true,
                    'backgroundColor' => $bgColor,
                    'borderColor' => $bgColor,
                    //'tension' => 0.4,
                ],
            ],
            'labels' => $municipalities,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
