  <h1>TEST</h1>
  <label class="container col-span-7 my-auto text-sm">Map Opacity:</label>
            <input type="number" class="col-span-5 text-black rounded-lg border text-xs" id="opacityInput" value="0.2" placeholder="Enter opacity (0-1)" min="0" max="1" step="0.1">
            
 <div class="bg-white shadow dark:bg-slate-900 z-0" onload="loadMe()"  id="map" style="height: 508px; width: 100%;"></div>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
         // Initialize the map after the DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
       
    });

    function loadMe()
    {
         var map = L.map('map').setView([51.505, -0.09], 13); // Example coordinates for the initial view
        
        // Set up the tile layer (OpenStreetMap in this case)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // You can add markers or other map features here as needed
        L.marker([51.5, -0.09]).addTo(map)
            .bindPopup("A pretty CSS3 popup.<br> Easily customizable.")
            .openPopup();
    }
    </script>
<x-filament-panels::page>
 
</x-filament-panels::page>
