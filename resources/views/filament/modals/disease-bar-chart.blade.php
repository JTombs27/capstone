<div>
@livewire(\App\Filament\Widgets\diseaseChart::class)
    <canvas id="diseaseBarChart" width="400" height="200"></canvas>
</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('diseaseBarChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        const chartData = {
            labels: ['Flu', 'Infection', 'Fever'],  // Example labels
            datasets: [{
                label: 'Disease Reports',
                data: [5, 10, 3],  // Example data
                backgroundColor: '#3B82F6',
            }]
        };

        new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
