{{-- 
        <div class="rounded-lg shadow bg-white p-4">
                <h2 class="text-lg font-semibold mb-2">Farm Locations</h2>
                <div id="map" style="height: 400px;"></div>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        //google streets 'http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}' 
                var googleStreets2 = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}',{
                    maxZoom: 15,
                    minZoom: 7,  
                    subdomains:['mt0','mt1','mt2','mt3']
                });

                var map2 = L.map('map', {
                    center: [7.5547,126.1404],
                    zoom: 9,
                    layers: [googleStreets2]
                });
                var municipalities = @json($this->getMunicipalities());
                
                var geoJsonLayerMunicipality;
                var currentOpacity = 1;//document.getElementById('opacityInput').value;
                fetch('/geoJSON/MunicipalBoundary.json')
                        .then(response => response.json())
                        .then(geojsonData => {
                            const blinkingBarangay = "Casoon"; 
                            let currentIndex = 0;
                            const Municipalcolors = ["red","transparent"];
                            geoJsonLayerMunicipality = L.geoJSON(geojsonData, {
                                style: function (feature) 
                                {
                                    var municipality = municipalities.find(m =>  m.municipality_name.toLowerCase() == feature.properties.MUN.toLowerCase());
                                    var color = municipality ? municipality.color : 'orange';
                                     
                                    return { color: 'white',
                                        stroke:true, 
                                        weight: 2, 
                                        fillOpacity:currentOpacity,
                                        fillColor:color,
                                         fillOpacity:0.8};
                                },
                                onEachFeature: function (feature, layer) {
                                    if (feature.properties && feature.properties.MUN) 
                                    {
                                       
                                        if(feature.properties.MUN.toLowerCase() == "new bataan" || feature.properties.MUN.toLowerCase() == "maco")
                                        {
                                            layer.blink = true;
                                            
                                        }

                                        layer.bindPopup("Name: " + feature.properties.MUN,{
                                                    permanent: false,
                                                    direction: 'top',
                                                });
                                        // Handle click on the polygon
                                            layer.on('dblclick', function (e) {
                                                // Get the clicked coordinates
                                                const clickedCoordinates = e.latlng;

                                                // Trigger the map's click handler manually
                                                map.fire('dblclick', {
                                                    latlng: clickedCoordinates,
                                                    layerPoint: e.layerPoint,
                                                    containerPoint: e.containerPoint,
                                                    originalEvent: e.originalEvent
                                                });

                                                // Optional: Prevent propagation to other events
                                                L.DomEvent.stopPropagation(e);
                                            });

                                           // Mouseover event: Highlight the polygon and show details
                                            layer.on('mouseover', function (e) {
                                                // Highlight the polygon
                                                e.target.setStyle({
                                                    color: 'blue',
                                                    weight: 1,
                                                    fillOpacity: 0.5,
                                                });

                                                // Show details in a tooltip
                                                layer.bindTooltip("Municipality: " + feature.properties.MUN, {
                                                    permanent: false,
                                                    direction: 'top',
                                                }).openTooltip(e.latlng);

                                            });

                                            // Mouseout event: Reset the style and remove details
                                            layer.on('mouseout', function (e) {
                                                // Reset the style to default
                                                geoJsonLayerMunicipality.resetStyle(e.target);

                                                // Remove the tooltip
                                                e.target.closeTooltip();
                                            });
                                    }
                                }
                            }).addTo(map2);

                             // Set up the blinking effect
                            setInterval(() => {
                                currentIndex = (currentIndex + 1) % Municipalcolors.length;

                                geoJsonLayerMunicipality.eachLayer(layer => {
                                    // Check if this layer should blink
                                    if (layer.blink) {
                                        layer.setStyle({
                                            fillColor: Municipalcolors[currentIndex],
                                            fillOpacity:1,
                                            stroke:true,
                                            opacity:1,
                                            color:"red"
                                        });
                                    }
                                });
                            }, 500); // Blink every 1 second
                        })
                        .catch(error => {
                            console.error("Error loading GeoJSON:", error);
                        });
                    });
                </script>
            </div> --}}

<div class="rounded-lg shadow bg-white p-4" wire:ignore>
    <h2 class="text-lg font-semibold mb-2">Farm Locations</h2>
    <div id="map" style="height: 400px;"></div>
  <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <!-- Leaflet JS -->
        @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function initMap() {
                alert("TEST");
                var map = L.map('map').setView([7.84386, 125.97996], 11);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Example: Add markers dynamically (Replace with real data)
                var farms = @json($this->getMunicipalities());
                console.log(farms);
                farms.forEach(function (farm) {
                    L.marker([farm.latitude, farm.longitude])
                        .addTo(map)
                        .bindPopup(`<b>${farm.owner_firstname} ${farm.owner_lastname}</b><br>${farm.farm_address}`);
                });
            }

            Livewire.hook('message.processed', (message, component) => {
                initMap();
            });

            initMap(); // Initial load
        });
    </script>
    @endpush
</div>


