
<div class="grid grid-cols-12 mt-5 gap-2 p-1">
     <div class="md:col-span-8 lg:col-span-8 col-span-12 grid grid-cols-12 gap-2">
        <div class="col-span-12 gap-y-2">
            @livewire(\App\Filament\Widgets\StatsOverview::class)
            
            @livewire(\App\Filament\Widgets\BlogPostsChart::class)

            @livewire(\App\Filament\Widgets\diseaseChart::class)
        </div>
     </div>
    <div class="md:col-span-4 lg:col-span-4 col-span-12 ">
        <div class=" p-4 bg-slate-900 grid grid-cols-2" style="border-radius: 10px 10px 0px 0px;">
            <label class="font-bold text-gray-700 text-white col-span-1">ASF ZONE STATUS</label>
            <div class="col-span-1 text-right">
                     <button type="submit"  wire:click="openModalZone" style="" class="border rounded px-2  text-purple-600 hover:border-transparent hover:bg-purple-600 hover:text-white active:bg-purple-700">
                        <small style="font-size: 12px !important;">Manage</small>
                    </button>
            </div>
        </div>
        <div class="shadow bg-slate-900 z-10" wire:ignore  id="map2" style="height: 350px; width: 100%;"></div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                    document.addEventListener('DOMContentLoaded', function () 
                    {
                        
                         let iframe = document.getElementById("crystalReportViewer");
                         iframe.style.height = window.innerHeight * 0.8 + "px"; // 80% of viewport
                        //google streets 'http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}' 
                        var googleStreets2 = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}',{
                            maxZoom: 9,
                            minZoom: 9,  
                            subdomains:['mt0','mt1','mt2','mt3']
                        });

                        var map2 = L.map('map2', {
                            center: [7.542,126.16],
                            zoom: 9,
                            layers: [googleStreets2]
                        });
                        var municipalities = @json($this->getMunicipalities());
                        
                        var geoJsonLayerMunicipality;
                        var currentOpacity = 1;//document.getElementById('opacityInput').value;
                        fetch('/geoJson/MunicipalBoundary.json')
                                .then(response => response.json())
                                .then(geojsonData => {
                                    const blinkingBarangay = "Casoon"; 
                                    let currentIndex = 0;
                                    const Municipalcolors = ["red","transparent"];
                                    geoJsonLayerMunicipality = L.geoJSON(geojsonData, {
                                        style: function (feature) 
                                        {
                                            var municipality = municipalities.find(m =>  m.municipality.municipality_name.toLowerCase() == feature.properties.MUN.toLowerCase());
                                            var color = municipality ? municipality.color_code : 'orange';
                                            
                                            return { color: 'white',
                                                stroke:true, 
                                                weight: 2, 
                                                fillOpacity:currentOpacity,
                                                fillColor:color,
                                                fillOpacity:1};
                                        },
                                        onEachFeature: function (feature, layer) {
                                            if (feature.properties && feature.properties.MUN) 
                                            {
                                            
                                                 let municipality = municipalities.find(m => 
                                                    m.municipality.municipality_name.toLowerCase() === feature.properties.MUN.toLowerCase()
                                                );

                                                let color = municipality ? municipality.color_code : 'orange';

                                                // If the municipality's fillColor is red, add it to blinkingLayers
                                                if (color === "red") {
                                                    layer.blink = true;
                                                }
                                                // if(feature.properties.MUN.toLowerCase() == "new bataan" || feature.properties.MUN.toLowerCase() == "maco")
                                                // {
                                                //     layer.blink = true;
                                                // }

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
        <div class="col-span-12 dashboardx p-[0.5px]">
              @livewire(\App\Filament\Widgets\municipalASFZoning::class)
        </div>
    </div>
    <div class="col-span-12">

        <x-filament::modal id="modalZone" slide-over class="z-[999]" width="5xl" >
            <x-slot name="heading">
                ASF Zoning
            </x-slot>
            <div class="col-span-12 h-[500px]"> 
                    <iframe id="crystalReportViewer"  src="http://localhost:60308/CrystalReportMVC/ViewReport?par_value=15"  width="100%" height="100%"></iframe>
            </div>

            <form  wire:submit.prevent="save()">
                 {{ $this->form }}
                <x-slot name="footer">
                    <div class="col-span-12 text-right">
                    <button type="submit"  wire:click="save()" class="px-4 py-2 bg-blue-500 text-white rounded pull-right hover:bg-blue-600">
                        Save
                    </button>
                    </div>
                </x-slot>
            </form>
        </x-filament::modal>
    </div>
</div>