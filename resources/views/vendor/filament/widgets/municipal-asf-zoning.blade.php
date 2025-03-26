<x-filament::widget>
    <x-filament::card>
        <div class="flex justify-between">
            <h2 class="text-lg font-bold">Municipal ASF Zoning Report</h2>
            <button onclick="printChart()" class="px-4 py-2 bg-blue-600 text-white rounded">
                Print Report
            </button>
        </div>

        {{ $this->chart }}

    </x-filament::card>

    <script>
        function printChart() {
            let chartCanvas = document.querySelector('canvas');
            let newWindow = window.open('', '', 'width=800,height=600');
            newWindow.document.write('<img src="' + chartCanvas.toDataURL() + '"/>');
            newWindow.document.close();
            newWindow.print();
        }
    </script>
</x-filament::widget>
