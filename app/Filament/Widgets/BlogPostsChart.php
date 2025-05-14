<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\DB;
use App\Models\Municipality;
use Filament\Widgets\ChartWidget;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Helpline Report Chart';
    protected static ?int $sort = 2;

    public ?int $filter_year_from = null;
    public ?int $filter_year_to = null;

    protected static ?string $maxHeight = "255px";

    protected function getData(): array
    {
        $query = Municipality::orderBy('municipality_name');

        $municipalities = $query->pluck('municipality_name')->toArray();

        $bgColor = $query->pluck('color')
            ->map(fn($color) => strtolower($color))
            ->toArray();

        $helpLineCountsQuery = Municipality::leftJoin('helplines', 'municipalities.id', '=', 'helplines.query_municipality')
            ->select('municipalities.id', DB::raw('COUNT(helplines.id) as help_line_count'))
            ->groupBy('municipalities.id')
            ->orderBy('municipalities.municipality_name');

        if ($this->filter_year_from && $this->filter_year_to) {
            $helpLineCountsQuery->whereBetween(DB::raw('YEAR(helplines.created_at)'), [$this->filter_year_from, $this->filter_year_to]);
        }

        $helpLineCounts = $helpLineCountsQuery->pluck('help_line_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Report Cases',
                    'data' => $helpLineCounts,
                    'fill' => true,
                    'backgroundColor' => $bgColor,
                    'borderColor' => $bgColor,
                ],
            ],
            'labels' => $municipalities,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return true; // Required for dynamic property updates
    }

    protected function getListeners(): array
    {
        return [
            'updateYearRange' => '$refresh',
        ];
    }
}
