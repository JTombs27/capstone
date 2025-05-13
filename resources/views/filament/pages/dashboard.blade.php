
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
                <select id="filter_year_from" wire:model="filter_year_from"  class="border-gray-300 rounded px-2 py-1 flext-items w-full" style="font-size: 12px;">
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
            <div class="col-span-12">
                <div id="chart" style="width: 100% !important; height: 340px;"></div>
            </div>
        </div>
     </div>
     <div class="md:col-span-3 lg:col-span-3 col-span-12 grid grid-cols-12 gap-2">
        <div class="col-span-12"> @livewire(\App\Filament\Widgets\StatsOverview::class)</div>
     </div>
     <div class="col-span-12 gap-y-2 grid grid-cols-12 gap-2">
        <div class="col-span-6">@livewire(\App\Filament\Widgets\BlogPostsChart::class)</div>
        <div class="col-span-6">@livewire(\App\Filament\Widgets\diseaseChart::class)</div>
      

        
    </div>

     @push('scripts')
     <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
     <script>
          var options = {
                series: [{
                    name: 'ASF',
                    data: [4,3,10,9,29,19,22,9,12,7,19,5]
                },
                {
                    name: 'CSF',
                    data: [19,22,9,12,7,19,38,54,3,10,9,29]
                },
                {
                    name: 'Avian Influenza (AI)/ HPIA',
                    data: [19,19,38,22,9,12,7,54,3,9,29,10]
                }
            ],
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
                    curve: 'straight'
                },
                xaxis: {
                    // type: 'datetime',
                    categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    // tickAmount: 10,
                    // labels: {
                    //     formatter: function (value, timestamp, opts) {
                    //         return opts.dateFormatter(new Date(timestamp), 'dd MMM');
                    //     }
                    // }
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

            var chart;

            window.addEventListener('load', () => {
                chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();

                // Give it a short delay to ensure layout is stable
                setTimeout(() => {
                    chart.resize();
                }, 500);
            });

            // Optional: Also resize on window resize
            window.addEventListener('resize', () => {
                if (chart) {
                    chart.resize();
                }
            });
     </script>
     @endpush
</div>