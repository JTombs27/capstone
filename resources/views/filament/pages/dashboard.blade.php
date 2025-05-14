
@php
    $currentYear = date('Y');
    $years = range($currentYear, 1900);
    
@endphp
<div class="grid grid-cols-12 mt-5 gap-2 p-1">
     <div class="md:col-span-9 lg:col-span-9 col-span-12 grid grid-cols-12 gap-2">
        <div class="bg-white border  p-4 rounded-lg shadow-md grid grid-cols-12  gap-x-2 col-span-12" style="font-size: 12px;">
            <div class="col-span-1 pt-2">
                <label><b>Filter</b></label>
            </div>
            <div class="col-span-1 pt-2 text-right">
                <label>From :</label>
            </div>
            <div class="col-span-2">
                <select id="filter_year_from" wire:model="filter_year_from" onchange="RenderForcasting()" class="border-gray-300 rounded px-2 py-1 flext-items w-full" style="font-size: 12px;">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 pt-2 text-right">
                <label>To :</label>
            </div>
            <div class="col-span-2">
                <select id="filter_year_to" wire:model="filter_year_to" class="border-gray-300 rounded px-2 py-1 flext-items w-full" style="font-size: 12px;">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="bg-white border  p-4 rounded-lg shadow-md grid grid-cols-12  gap-x-2 col-span-12">
            <div class="col-span-7">
                <div id="chart" style="width: 100% !important; height: 340px;"></div>
            </div>
            <div class="col-span-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-2 py-2 font-semibold text-gray-700">Disease</th>
                            <th class="text-left px-2 py-2 font-semibold text-gray-700">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($this->forecastResults as $row)
                            <tr>
                                <td class="px-2 py-1 text-gray-800">{{ $row['disease'] }}</td>
                                <td class="px-2 py-1 text-gray-800">{{ $row['trend'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
     </div>
     <div class="md:col-span-3 lg:col-span-3 col-span-12 grid grid-cols-12 gap-2">
        <div class="col-span-12"> @livewire(\App\Filament\Widgets\StatsOverview::class)</div>
     </div>
     <div class="col-span-12 gap-y-2 grid grid-cols-12 gap-2">
        <div class="col-span-12">@livewire(\App\Filament\Widgets\BlogPostsChart::class)</div>
        {{-- <div class="col-span-6">@livewire(\App\Filament\Widgets\diseaseChart::class)</div> --}}
    </div>

     @push('scripts')
     <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
     <script>
            let chart;
            function RenderForcasting()
            {
                let yearFrom = document.getElementById('filter_year_from').value;
                let yearTo = document.getElementById('filter_year_to').value;
                window.dispatchEvent(new CustomEvent('updateYearRange', {  filter_year_from: parseInt(yearFrom),
                    filter_year_to: parseInt(yearTo) }));
             

                const forecastResults = @json($forecastResults);
                const series = forecastResults.map(item => {
                        const data = item.forecast.map(f => f.value);
                        return {
                            name: item.disease,
                            data: data
                        };
                    });
                const categories = forecastResults[0]?.forecast.map(f => f.month) || [];
                var options = {
                        series: series,
                        chart: {
                            height: 320,
                            type: 'line',
                            zoom: {
                                enabled: false
                            }
                        },
                        forecastDataPoints: {
                            count: 7
                        },
                        stroke: {
                            width: 2,
                            curve: 'smooth'
                        },
                        xaxis: {
                            categories: categories,
                        },
                        title: {
                            text: 'Disease Forecast',
                            align: 'left',
                            style: {
                                fontSize: "16px",
                                color: '#666'
                            }
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'dark',
                                gradientToColors: ['#172554'],
                                shadeIntensity: 1,
                                type: 'horizontal',
                                opacityFrom: 1,
                                opacityTo: 1,
                                stops: [0, 100, 100, 100]
                            },
                        }
                    };

                    if (chart) 
                    {
                        chart.updateOptions({
                            xaxis: { categories: categories },
                        });
                        chart.updateSeries(series);
                    } else {
                        chart = new ApexCharts(document.querySelector("#chart"), options);
                        chart.render();
                    }
            }
            window.addEventListener('load', () => {
                RenderForcasting();
            });

            window.addEventListener('forecast-updated', () => {
                RenderForcasting(); // this will re-render your Apex chart
            });
     </script>
     @endpush
</div>