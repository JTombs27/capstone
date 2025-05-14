<div class="col-span-12">
    <h1>TESTING</h1>

    <div
        x-data="{
            chart: null,
            initChart() {
                if (this.chart) return; // Prevent duplicate rendering

                const options = {
                    chart: {
                        type: 'bar',
                        height: 300
                    },
                    series: [{
                        name: 'Report Cases',
                        data: [5, 2, 6, 8] 
                    }],
                    xaxis: {
                        categories: ['Mun A', 'Mun B', 'Mun C', 'Mun D'] 
                    },
                    title: {
                        text: 'Helpline Reported Cases by Municipality',
                        align: 'left',
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold',
                            color: '#263238'
                        }
                    }
                };

                this.chart = new ApexCharts(this.$refs.chart, options);
                this.chart.render();
            }
        }"
        x-init="initChart()"
    >
        <div x-ref="chart" style="width: 100%; height: 340px;"></div>
    </div>
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush
@endonce
