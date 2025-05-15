
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
                <select id="filter_year_from" wire:model="filter_year_from" wire:change="onYearChange" class="border-gray-300 rounded px-2 py-1 flext-items w-full" style="font-size: 12px;">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 pt-2 text-right">
                <label>To :</label>
            </div>
            <div class="col-span-2">
                <select id="filter_year_to" wire:model="filter_year_to" wire:change="onYearChange" class="border-gray-300 rounded px-2 py-1 flext-items w-full" style="font-size: 12px;">
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
     <div class="col-span-12 gap-y-2 grid grid-cols-12 gap-2" >
        <div class="col-span-7 bg-white border shadow-md rounded-lg p-4 h-[300px]" id="barChart"></div>
        <div class="col-span-5 overflow-x-auto bg-white border shadow-md rounded-lg p-4">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-2 py-2 font-semibold text-gray-700">Disease</th>
                        <th class="text-left px-2 py-2 font-semibold text-gray-700">Month - Year</th>
                        <th class="text-left px-2 py-2 font-semibold text-gray-700 text-right">Reports</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($this->diseaseTrends as $row)
                        <tr>
                            <td class="px-2 py-1 text-gray-800">{{ $row["disease_name"] }}</td>
                            <td class="px-2 py-1 text-gray-800">{{ \Carbon\Carbon::createFromFormat('m', $row['month'])->format('F') }} {{$row["year"]}}</td>
                            <td class="px-2 py-1 text-gray-800 text-right">{{$row["case_count"]}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
        
    </div>

     @push('scripts')
     <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
     <script>
            let chart;
            let barChart;
            function filterChange()
            {
                RenderForcasting();
                RenderBarchart();
            }
            function RenderForcasting()
            {
                let yearFrom = document.getElementById('filter_year_from').value;
                let yearTo   = document.getElementById('filter_year_to').value;
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
                            count: forecastResults[0]["forecastDataPoints"]
                        },
                        stroke: {
                            width: 2,
                            curve: 'smooth'
                        },
                        xaxis: {
                            categories: categories,
                        },
                        title: {
                            text: 'Disease Forecast '+forecastResults[0]["forcastedyear"],
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

            function getMedian(arr) 
            {
                if (!arr.length) return null;

                const sorted = [...arr].sort((a, b) => a - b);
                const mid = Math.floor(sorted.length / 2);

                if (sorted.length % 2 === 0) {
                    // Even: average of two middle values
                    return (sorted[mid - 1] + sorted[mid]) / 2;
                } else {
                    // Odd: return middle value
                    return sorted[mid];
                }
            }

            function RenderBarchart()
            {
                const municipal_data = @json($this->getMunicipalityChartData());
                var categories = municipal_data.map(item => item.municipality);
                var series     = municipal_data.map(item => item.value);
                var colors     = municipal_data.map(item => item.color);
                var options = 
                {
                    series: 
                        [
                            {name: 'Report Cases',data: series}
                        ],
                        chart: {
                        type: 'bar',
                        height: 300
                        },
                        annotations: 
                        {
                            // xaxis: [{
                            //     x: getMedian([1,2,3,1,2,2,4]),
                            //     borderColor: '#00E396',
                            //     label: {
                            //         borderColor: '#00E396',
                            //             style: {
                            //                 color: '#fff',
                            //                 background: '#00E396',
                            //             },
                            //     text: 'Median data',
                            //     }
                            // }],
                            yaxis: [{
                                y: getMedian(series),
                                borderColor: '#00E396',
                                label: {
                                    borderColor: '#00E396',
                                        style: {
                                            color: '#fff',
                                            background: '#00E396',
                                        },
                                    text: 'Median data',
                                    }
                            }]
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                distributed: true // <<< This makes each bar use a different color
                            }
                        },
                        colors: colors,
                        dataLabels: 
                        {
                            enabled: true,
                        },
                        xaxis: 
                        {
                            categories: categories,
                        },
                        title: 
                        {
                            text: 'Helpline Reported Cases by Municipality',
                            align: 'left',  // 'left', 'center', or 'right'
                            margin: 2,
                            offsetY: 2,
                            style: {
                            fontSize: '12px',
                            fontWeight: 'bold',
                            color: '#263238'
                            }
                        },
                        legend: {
                            position: 'right', // Move the legend to the right
                            horizontalAlign: 'center', // Optional: Align the legend horizontally in the center
                            verticalAlign: 'middle', // Optional: Align the legend vertically in the middle
                            floating: false, // Optional: Make the legend float above the chart area
                        },
                        grid: 
                        {
                            xaxis: {
                                lines: {
                                show: true
                                }
                            },
                            yaxis: {
                                lines: {
                                show: true
                                }
                            }

                        },
                        yaxis: 
                        {
                            reversed: false,
                            axisTicks: 
                            {
                                show: true
                            }
                        },
                        // tooltip: {
                        //         y: {
                        //             formatter: function (val, opts) {
                        //                 const index = opts.dataPointIndex;
                        //                 const name = labels[index];
                        //                 return `${name}: ${val}`;
                        //             }
                        //         }
                        //     }

                    };

                    if (barChart) 
                    {
                        barChart.updateOptions({
                            xaxis: { categories: categories },
                        });
                        barChart.updateSeries([{name: 'Report Cases',data: series}]);
                    } else {
                        barChart = new ApexCharts(document.querySelector("#barChart"), options);
                        barChart.render();
                    }
            }

            window.addEventListener('load', () => {
                RenderForcasting();
                RenderBarchart();
            });
            
            window.addEventListener('filter-update', (event) => {
                console.log(event);
                const municipal_data = event.detail[0].municipal_data;
                const forecastResults = event.detail[0].forecast_data;

                // Render bar chart
                const categories = municipal_data.map(item => item.municipality);
                const series     = municipal_data.map(item => item.value);
                const colors     = municipal_data.map(item => item.color);
                const median     = getMedian(series);
                if (barChart) 
                {
                    barChart.updateOptions({
                        xaxis: { categories: categories },
                        colors: colors,
                        annotations: {
                            yaxis: [{
                                y: median, // Update the median value
                                borderColor: '#00E396',
                                label: {
                                    borderColor: '#00E396',
                                    style: {
                                        color: '#fff',
                                        background: '#00E396',
                                    },
                                    text: `Median data: ${median}`, // Update label text
                                }
                            }]
                        }
                    });
                    barChart.updateSeries([{ name: 'Report Cases', data: series }]);
                } else {
                    // Handle init if needed
                }

                // Render forecast chart
                const forecastSeries = forecastResults.map(item => {
                    return {
                        name: item.disease,
                        data: item.forecast.map(f => f.value)
                    };
                });

                const forecastCategories = forecastResults[0]?.forecast.map(f => f.month) || [];

                if (chart) {
                    chart.updateOptions({
                        xaxis: { categories: forecastCategories },
                    });
                    chart.updateSeries(forecastSeries);
                }

                setTimeout(() => {
                    // Refresh charts after a short delay
                    if (chart) {
                        chart.updateOptions({});
                        chart.render();
                    }
                    if (barChart) {
                        barChart.updateOptions({});
                        barChart.render();
                    }
                }, 5);
            });


     </script>
     @endpush
</div>