
@php
    $currentYear = date('Y');
    $years = range($currentYear, 1900);
@endphp
<div class="grid grid-cols-12 relative overflow-hidden">
     <style>
        .fi-main { padding: 0 !important;}
        .leaflet-overlay-pane {
            z-index: 700 !important;
        }

        .leaflet-heatmap-layer {
            pointer-events: none !important;
        }
    </style>
    <div class="md:col-span-12 lg:col-span-12 col-span-12 grid grid-cols-12" wire:ignore>
        <div class="absolute left-4 z-20 bg-white top-[12px] p-4 rounded-lg shadow-lg grid grid-cols-12 w-[700px] gap-x-2" style="font-size: 12px;">
            <h4 class="font-bold mb-[2px] col-span-1  pt-2">Filters</h4>
            <div class="col-span-3 grid grid-cols-12 gap-x-2">
                <label class="col-span-4 inline mb-1 pt-2 text-right">From: </label>
                <div class="col-span-8">
                <select id="filter_year_from" class="border-gray-300 rounded px-2 py-1 w-full" style="font-size: 12px;">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="col-span-3 grid grid-cols-12 gap-x-2">
                <label class="col-span-4 inline mb-1 pt-2 text-right">To: </label>
                <div class="col-span-8">
                <select id="filter_year_to" class="border-gray-300 rounded px-2 py-1 w-full" style="font-size: 12px;">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
                </div>
            </div>
           <div class="col-span-5 grid grid-cols-12 gap-x-2">
                <label class="col-span-4 inline mb-1 pt-2">Municipality: </label>
                <div class="col-span-8">
                <select id="filter_municipality"  class="border-gray-300 rounded px-2 py-1 w-full" style="font-size: 12px;">
                    <option value="">All</option>
                     @foreach ($this->municipalities as $municipalities)
                        <option value="{{ $municipalities->municipality_name }}">{{ $municipalities->municipality_name }}</option>
                    @endforeach
                </select>
                </div>
            </div>
        </div>
        <div class="absolute top-[85px] left-4 z-20 bg-white p-4 rounded-lg shadow-lg w-[200px]" style="font-size: 12px;">
            {{-- <h4 class="font-bold mb-[2px]">Testing</h4> --}}
            <div class="col-span-12 gap-y-4">
                 <div class="grid grid-cols-12 mb-[10px]">
                    <div class="col-span-12">
                       <b>Display Options</b>
                    </div>
                </div>
                <div class="grid grid-cols-12 mb-[10px]">
                    <label class="col-span-12 grid grid-cols-12">
                        <div class="col-span-11 flex">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>

                            <span style="padding-top:1px;">&nbsp;View Heatmap</span>
                        </div>
                        <div class="col-span-1" style="padding-top:1px;"><input type="checkbox" id="showHeatMapButton"  name="accept_terms" class="form-checkbox text-primary-600"></div>
                    </label>
                </div>
                <div class="grid grid-cols-12 mb-[10px]">
                    <label class="col-span-12 grid grid-cols-12">
                        <div class="col-span-11 flex"> 
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                        </svg>
                        <span style="padding-top:1px;">&nbsp;View Municipalities</span>
                        </div>
                        <div class="col-span-1"  style="padding-top:1px;"><input type="checkbox" id="showMunicipality"  name="accept_terms" class="form-checkbox text-primary-600"></div>
                    </label>
                </div>
                <div class="grid grid-cols-12 mb-[10px]">
                    <label class="col-span-12 grid grid-cols-12">
                        <div class="col-span-11 flex"> 
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                        </svg>
                        <span style="padding-top:1px;">&nbsp;View Barangays</span>
                        </div>
                        <div class="col-span-1"  style="padding-top:1px;"><input type="checkbox" id="showBarangays"  name="accept_terms" class="form-checkbox text-primary-600"></div>
                    </label>
                </div>
                <div  class="grid grid-cols-12 mb-[10px]">
                    <label class="col-span-12 grid grid-cols-12" onclick="document.getElementById('asfLegends').classList.toggle('hidden')">
                        <div class="col-span-11 flex" onclick="document.getElementById('asfLegends').classList.toggle('hidden')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                              </svg>
                            <span style="padding-top:1px;">&nbsp;View ASF Zoning</span>
                        </div>
                        <div class="col-span-1" style="padding-top:1px;"><input type="checkbox" id="showASFZoning"  name="accept_terms" class="form-checkbox text-primary-600"></div>
                    </label>
                </div>
                <hr />
                <div class="grid grid-cols-12 mt-[10px]">
                    <label class="col-span-12 grid grid-cols-12">
                        <div class="col-span-11">
                       Show All Farms
                        </div>
                        <div class="col-span-1"><input type="checkbox" name="option" value="all" class="form-checkbox text-primary-600 single-checkbox right-0"></div>
                    </label>
                </div>
                <div class="grid grid-cols-12">
                    <label class="col-span-12 grid grid-cols-12">
                        <div class="col-span-11">
                      Poultry Farms
                        </div>
                        <div class="col-span-1"><input type="checkbox" name="option" value="Manok" class="form-checkbox text-primary-600 single-checkbox right-0"></div>
                    </label>
                </div>
                 <div class="grid grid-cols-12">
                    <label class="col-span-12 grid grid-cols-12">
                        <div class="col-span-11">
                     Swine Farms
                        </div>
                        <div class="col-span-1"><input type="checkbox" name="option" value="Baboy" class="form-checkbox text-primary-600 single-checkbox right-0"></div>
                    </label>
                </div>
            </div>
        </div>
       
        <div class="shadow bg-slate-900 z-10 col-span-12 grid grid-cols-12 w-full" style="height: calc(100vh - 64px);"  id="map" ></div>
        <div class="absolute top-[85px] right-0 z-10 rounded-lg shadow-lg w-[250px]" style="font-size: 12px;">
            <div class="grid grid-cols-12">
                <div class="col-span-12" id="charts"></div>
            </div>
        </div>
        <div id="asfLegends"  class="hidden absolute bottom-5 left-1/2 -translate-x-1/2 z-10 bg-white rounded-md p-2" style="font-size: 12px;">
            <div class="col-lg-12">
                <b>Zoning Legends:</b>
            </div>
            <div class="flex item-center gap-2 mt-1">
                <span class="w-4 h-4 rounded-sm bg-yellow-400 inline-block"></span><span>Surveillance Zone</span>
                <span class="w-4 h-4 rounded-sm bg-pink-400 inline-block"></span>  <span>Buffer Zone</span>
                <span class="w-4 h-4 rounded-sm bg-red-500 inline-block"></span>    <span>Infected Zone</span>
            </div>
        </div>
    @push('scripts')
        <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
        let geoJsonLayerMunicipality;
       
        document.addEventListener('DOMContentLoaded', function () 
            {
                var filteredDataMain;
                const currentYear = new Date().getFullYear();
                document.getElementById('filter_year_from').addEventListener('change', showDiseaseCaseHeatMap);
                document.getElementById('filter_year_from').value   = 2000;
                document.getElementById('filter_year_to').value     = currentYear;
                
                 var googleStreets = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{
                    maxZoom: 17,
                    minZoom: 7,  
                    subdomains:['mt0','mt1','mt2','mt3']
                });
                
                var map = L.map('map', {
                            center: [7.5547,126.1404],
                            zoom: 10,
                            layers: [googleStreets],
                            zoomControl: false 
                        }); 
                L.control.zoom({
                    position: 'topright' // Default is 'topleft'
                }).addTo(map);
                map.createPane('polygonsPane');
                map.getPane('polygonsPane').style.zIndex = 600;
                map.createPane('heatPane');
                map.getPane('heatPane').style.zIndex = 700;
                map.createPane('markerPane'); // optional; markers use default pane
                map.getPane('markerPane').style.zIndex = 750;

                var monitoredDiseases   = @json($this->getDiseaseMonitored(2000,2025));    
                var ASFZoningData       = @json($this->getASFZoning());

                const heatPoints        = monitoredDiseases.map(item => [item.latitude, item.longitude, item.affected_count]);
                filteredDataMain        = monitoredDiseases;
                var heatLayer = L.heatLayer(heatPoints, {
                        radius: 25,
                        blur: 15,
                        maxZoom: 14,
                        pane:'heatPane'
                });

                var allFarmType = L.layerGroup(); 
                var data        = @json($this->getFarms());
                var baboyan     = L.layerGroup();
                var manokan     = L.layerGroup();
                const diseaseLayers = L.layerGroup();

                // // Add farm markers
                data.forEach(function (farm) 
                {
                    var iconUrl = '/images/farmMarker.png'
                    console.log(farm);
                    if(farm.animal_name != "All")
                    {
                        iconUrl = '/images/'+farm.animal_name+'.png';
                    }
                    var marker = L.marker([parseFloat(farm.latitude), parseFloat(farm.longitude)], 
                    {
                        icon: L.icon({
                            iconUrl: iconUrl,
                            iconSize: [30, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34]
                        })
                    })
                    .bindPopup(`
                    <div style="z-index:800 !important;">
                         <center>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-10">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                            </svg>
                       
                             <b>`+farm.owner_firstname+' '+farm.owner_lastname+`</b>
                        </center>
                    </div>
                  `);
                    
                    if(farm.animal_name == "Baboy")
                    {
                        baboyan.addLayer(marker);
                    }

                    if(farm.animal_name == "Manok")
                    {
                        manokan.addLayer(marker);
                    }
                    allFarmType.addLayer(marker);
                });
                var year_from           = document.getElementById('filter_year_from').value;
                var year_to             = document.getElementById('filter_year_to').value; 
                var ASFGeoJson;

                fetch('/geoJson/MunicipalBoundary.json')
                                .then(response => response.json())
                                .then(geojsonData => {
                                    const blinkingBarangay = "Casoon"; 
                                    let currentIndex = 0;
                                    const Municipalcolors = ["red","transparent"];
                                    ASFGeoJson = L.geoJSON(geojsonData, {
                                        pane: 'polygonsPane',
                                        style: function (feature) 
                                        {
                                            var municipality = ASFZoningData.find(m =>  m.municipality.municipality_name.toLowerCase() == feature.properties.MUN.toLowerCase());
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
                                            
                                                 let municipality = ASFZoningData.find(m => 
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
                                                        layer.bindTooltip("🏛️: " + feature.properties.MUN, {
                                                                permanent: false,
                                                                direction: 'top',
                                                            }).openTooltip(e.latlng);

                                                    });

                                                    // Mouseout event: Reset the style and remove details
                                                    layer.on('mouseout', function (e) {
                                                        // Reset the style to default
                                                        ASFGeoJson.resetStyle(e.target);

                                                        // Remove the tooltip
                                                        e.target.closeTooltip();
                                                    });
                                            }
                                        }
                                    });

                                    // Set up the blinking effect
                                    setInterval(() => {
                                        currentIndex = (currentIndex + 1) % Municipalcolors.length;

                                        ASFGeoJson.eachLayer(layer => {
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
               
                var municipalities      = @json($this->getMunicipalities());
               
                var currentOpacity      = 0.2;

                fetch('/geoJson/MunicipalBoundary.json')
                        .then(response => response.json())
                        .then(geojsonData => 
                        {
                           
                            let currentIndex = 0;
                            const Municipalcolors = ["red","transparent"];
                            geoJsonLayerMunicipality = L.geoJSON(geojsonData, {
                                pane: 'polygonsPane',
                                style: function (feature) 
                                {
                                    var municipality = municipalities.find(m =>  m.municipality_name.toLowerCase() == feature.properties.MUN.toLowerCase());
                                    var color = "white"//"#f9a547"//municipality ? municipality.color : 'orange';
                                     
                                    return { 
                                        color: '#ac6516',
                                        stroke:true, 
                                        weight: 2, 
                                        fillColor:color,
                                        fillOpacity:0.5};
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
                                                    color: '#f9a547',
                                                    weight: 3,
                                                    fillOpacity: 0.3,
                                                    fillColor:"white"
                                                });

                                                layer.bindTooltip("🏛️: " + feature.properties.MUN, {
                                                    permanent: false,
                                                    direction: 'top',
                                                }).openTooltip(e.latlng);
                                            })
                                            ;

                                            // Mouseout event: Reset the style and remove details
                                            layer.on('mouseout', function (e) 
                                            {
                                                geoJsonLayerMunicipality.resetStyle(e.target);
                                                // Remove the tooltip
                                                e.target.closeTooltip();
                                            });
                                    }
                                }
                            })
                        })
                        .catch(error => {
                            console.error("Error loading GeoJSON:", error);
                        });
                    
                var geoJsonLayerB;

                fetch('/geoJson/BarangayBoundary.json')
                        .then(response => response.json())
                        .then(geojsonData => 
                        {
                           
                            let currentIndex = 0;
                            const Municipalcolors = ["red","transparent"];
                            geoJsonLayerB = L.geoJSON(geojsonData, {
                                pane: 'polygonsPane',
                                style: function (feature) 
                                {
                                    var municipality = municipalities.find(m =>  m.municipality_name.toLowerCase() == feature.properties.MUN.toLowerCase());
                                    var color = municipality ? municipality.color : 'orange';
                                     
                                    return { 
                                        color: 'gray',
                                        stroke:true, 
                                        weight: 1, 
                                        fillColor:"gray",
                                        fillOpacity:0};
                                },
                                onEachFeature: function (feature, layer) 
                                {
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
                                                    fillColor:"#f9a547",
                                                    fillOpacity: 0.5,
                                                });

                                                // Show details in a tooltip
                                                layer.bindTooltip("🏘️: " + feature.properties.Brgy.toLowerCase(), {
                                                    permanent: false,
                                                    direction: 'top',
                                                }).openTooltip(e.latlng);

                                            });

                                            // Mouseout event: Reset the style and remove details
                                            layer.on('mouseout', function (e) {
                                                // Reset the style to default
                                                geoJsonLayerB.resetStyle(e.target);

                                                // Remove the tooltip
                                                e.target.closeTooltip();
                                            });
                                    }
                                }
                            });
                            showDiseaseCaseHeatMap();
                            showMunicipality();
                        })
                        .catch(error => {
                            console.error("Error loading GeoJSON:", error);
                        });

                function showFarms(farm_type,checkD) 
                {
                    if(!checkD)
                    {
                        allFarmType.removeFrom(map);
                        baboyan.removeFrom(map);
                        manokan.removeFrom(map);
                        return;
                    }

                    if (farm_type == "all") 
                    {
                        baboyan.removeFrom(map);
                        manokan.removeFrom(map);
                        allFarmType.addTo(map);
                    } else  if (farm_type == "Baboy") 
                    {
                        allFarmType.removeFrom(map);
                        manokan.removeFrom(map);
                        baboyan.addTo(map);
                    }
                      else  if (farm_type == "Manok") 
                    {
                        allFarmType.removeFrom(map);
                        baboyan.removeFrom(map);
                        manokan.addTo(map);
                    }
                }

                function showDiseaseCaseHeatMap()
                {
                     if (!document.getElementById('showHeatMapButton').checked) 
                        {
                            heatLayer.removeFrom(map);
                        }
                    else{

                        bringHeatToFront();
                            monitoredDiseases.forEach(point => 
                            {
                                L.circleMarker([point.latitude, point.longitude], {
                                    radius: 10,
                                    color: 'transparent',
                                    fillOpacity: 0,
                                    pane:"markerPane"
                                })
                                .on('click', () => {
                                    window.dispatchEvent(new CustomEvent('open-disease-modal', { detail: {details : point} }));
                                })
                                .addTo(map);
                            });
                    }
                  
                }

                function showBarangays()
                {
                    if (map.hasLayer(geoJsonLayerB)) 
                    {
                        geoJsonLayerB.removeFrom(map);
                    }
                    else
                    {
                        geoJsonLayerB.addTo(map);
                    }

                    if(document.getElementById('showHeatMapButton').checked)
                    {
                        bringHeatToFront()
                    } 
                }

                function showMunicipality()
                {
                    if (map.hasLayer(geoJsonLayerMunicipality)) 
                    {
                        geoJsonLayerMunicipality.removeFrom(map);
                    }
                    else
                    {
                        geoJsonLayerMunicipality.addTo(map);
                    }
                    
                   if(document.getElementById('showHeatMapButton').checked)
                    {
                        bringHeatToFront()
                    }
                }

                function filterByDiseaseType() 
                {
                     var fromYear  = document.getElementById('filter_year_from').value;
                     var toYear    = document.getElementById('filter_year_to').value;
                     var filterMun = document.getElementById('filter_municipality').value.toString().trim().toLowerCase();
                    let filtered = monitoredDiseases.filter(item => 
                                                        {
                                                            const reportYear = new Date(item.date_reported).getFullYear();
                                                            return reportYear >= fromYear && reportYear <= toYear;
                                                        });
                     if(filterMun != "")
                     {
                        filtered = filtered.filter(item => 
                                            {
                                                return item.municipal.municipality_name.toLowerCase() == filterMun;
                                            });
                     }
                    renderHeatmap(filtered);
                    reFreshChart();
                }


                function renderHeatmap(filteredData) 
                {
                    const heatPoints = filteredData.map(item => [
                        item.latitude,
                        item.longitude,
                        item["affected_count"]
                    ]);
                    heatLayer.setLatLngs(heatPoints);
                    filteredDataMain = filteredData;
                    bringHeatToFront();
                    filteredData.forEach(point => 
                    {
                        L.circleMarker([point.latitude, point.longitude], {
                            radius: 10,
                            color: 'transparent',
                            fillOpacity: 0,
                            pane:"markerPane"
                        })
                        .on('click', () => {
                            window.dispatchEvent(new CustomEvent('open-disease-modal', { detail: {details : point} }));
                        })
                        .addTo(map);
                    });
                }

                function showASFZoningMap()
                {
                    if(map.hasLayer(ASFGeoJson))
                    {
                        ASFGeoJson.removeFrom(map);
                    }
                    else{
                        ASFGeoJson.addTo(map);
                    }
                   
                }

                document.getElementById('showHeatMapButton').addEventListener('click', showDiseaseCaseHeatMap);
                document.getElementById('showBarangays').addEventListener('click', showBarangays);
                document.getElementById('filter_year_from').addEventListener('change', filterByDiseaseType);
                document.getElementById('filter_year_to').addEventListener('change', filterByDiseaseType);
                document.getElementById('filter_municipality').addEventListener('change', filterByDiseaseType);
                document.getElementById('showMunicipality').addEventListener('change', showMunicipality);
                document.getElementById('showASFZoning').addEventListener('click', showASFZoningMap);
                
                document.querySelectorAll('.single-checkbox').forEach(cb => {
                    cb.addEventListener('change', function () 
                    {
                        showFarms(this.value,this.checked);
                        if (this.checked) 
                        {
                            document.querySelectorAll('.single-checkbox').forEach(other => 
                            {
                                if (other !== this) other.checked = false;
                            });
                        }
                        else{
                        }
                    });
                    });
                document.getElementById('showHeatMapButton').checked = true;
                document.getElementById('showMunicipality').checked = true;

              
                function bringHeatToFront() 
                {
                    map.removeLayer(heatLayer);
                    heatLayer.addTo(map); // adds it back on top
                }

                var grouped = {};
                filteredDataMain.forEach(item => {
                    const name = item.municipal?.municipality_name || 'Unknown';
                    grouped[name] = (grouped[name] || 0) + 1;
                });

                var sorted = Object.entries(grouped).sort((a, b) => b[1] - a[1]);

                var labels = sorted.map(([name]) => name);
                var series = sorted.map(([, count]) => count);
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
                            xaxis: [{
                                x: getMedian(series),
                                borderColor: '#00E396',
                                label: {
                                    borderColor: '#00E396',
                                        style: {
                                            color: '#fff',
                                            background: '#00E396',
                                        },
                                text: 'Median data',
                                }
                            }],
                            // yaxis: [{
                            //     y: 'Maco',
                            //     y2: 'New Bataan',
                            //     label: {
                            //     text: 'Y annotation'
                            //     }
                            // }]
                        },
                        plotOptions: 
                        {
                            bar: {
                                horizontal: true,
                            }
                        },
                        dataLabels: 
                        {
                            enabled: true,
                        },
                        xaxis: 
                        {
                            categories: labels,
                        },
                        title: 
                        {
                            text: 'Reported Cases by Municipality',
                            align: 'left',  // 'left', 'center', or 'right'
                            margin: 2,
                            offsetY: 2,
                            style: {
                            fontSize: '12px',
                            fontWeight: 'bold',
                            color: '#263238'
                            }
                        },
                        grid: 
                        {
                            xaxis: {
                                lines: {
                                show: false
                                }
                            },
                            yaxis: {
                                lines: {
                                show: false
                                }
                            }

                        },
                        yaxis: 
                        {
                            reversed: true,
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

                var chart = new ApexCharts(document.querySelector("#charts"), options);
                chart.render();

                function reFreshChart()
                {
                    if(filteredDataMain.length > 0)
                    {
                        grouped = {};
                        var filterMun = document.getElementById('filter_municipality').value.toString().trim().toLowerCase();
                        console.log(filterMun);
                        if(filterMun != "")
                        {
                            filteredDataMain.forEach(item => {
                                grouped["Apiktado"] = (grouped["Apiktado"] || 0) + item.affected_count;
                                grouped["Sample"] = (grouped["Sample"] || 0) + item.sample_count;
                                grouped["Positive"] = (grouped["Positive"] || 0) + item.positive_count;
                            });

                            console.log(grouped);
                            sorted = Object.entries(grouped).sort((a, b) => b[1] - a[1]);
                            console.log(sorted);
                            labels = sorted.map(([name]) => name);
                            series = sorted.map(([, count]) => count);
                            sorted = Object.entries(grouped).sort((a, b) => b[1] - a[1]);

                            labels = sorted.map(([name]) => name);
                            series = sorted.map(([, count]) => count);
                            mySeries = [];
                            sorted.forEach(item => {
                                mySeries.push({name:item[0],data:[item[1]]})
                            });
                            chart.updateOptions({
                                xaxis: {
                                    categories: [filteredDataMain[0].municipal.municipality_name]
                                }
                                });
                            chart.updateSeries(mySeries);
                        }
                        else
                        {
                            filteredDataMain.forEach(item => {
                                const name = item.municipal?.municipality_name || 'Unknown';
                                grouped[name] = (grouped[name] || 0) + 1;
                            });

                            sorted = Object.entries(grouped).sort((a, b) => b[1] - a[1]);

                            labels = sorted.map(([name]) => name);
                            series = sorted.map(([, count]) => count);

                            chart.updateOptions({
                                xaxis: {
                                    categories: labels 
                                }
                                });
                                chart.updateSeries([
                                {
                                    name: 'Report Cases',
                                    data: series
                                }
                                ]);
                        }
                    }
                    else{
                        chart.updateOptions({
                                xaxis: {
                                    categories: ["No Data"]
                                }
                                });
                        chart.updateSeries([
                            {
                                name: 'Report Cases',
                                data: [0]
                            }
                            ]);
                    }
                   
                }

    });
        
      
</script>
@endpush
</div>
<x-filament::modal id="diseaseInfo" slide-over  class="z-[999]" alignment="center" width="md">
    <x-slot name="heading">
        Disease Information {{ $selectedDisease["owner_firstname"] ?? '' }}
    </x-slot>
        Municipality of: {{ $selectedDisease["municipal"]['municipality_name'] ?? '' }}<br/>
        Under Barangay of: {{ $selectedDisease["barangay"]['barangay_name'] ?? '' }}<br/>
        <p><b>Disease :</b>{{ $selectedDisease["disease"]['disease_description'] ?? '' }} 
        <i class="text-gray-500" style="font-size: 12px !important;">Sakit sa {{ $selectedDisease["animal"]['animal_name'] ?? ''}}</i>
        </p>
        <p><b>Apiktado :</b>{{ $selectedDisease["affected_count"] ?? '' }} 
        </p>
        <p><b>Preventions:</b><br>{{ $selectedDisease["disease"]['preventions'] ?? '' }}</p>
        <p><b>Treatments:</b><br>{{ $selectedDisease["disease"]['treatment'] ?? '' }}</p>
        <hr>
        <p>Area Registered Farm/s:</p>
        <div class="gap-y-1">
            @foreach ($registeredFarms as $item)
                <form wire:submit.prevent="sendSMS({{$item['id']}},{{$selectedDisease['id']}})" >
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                <img class="mx-auto block h-[50px] rounded-full sm:mx-0 sm:shrink-0" src="/images/farmerProfileBoy.png" alt="" />
                <div class="space-y-2 text-center sm:text-left">
                    <div class="space-y-0.5">
                    <p class="text-lg font-semibold text-black">{{$item["owner_firstname"]." ".$item["owner_lastname"]}} 
                        
                            <button type="submit" class="border rounded px-2  text-purple-600 hover:border-transparent hover:bg-purple-600 hover:text-white active:bg-purple-700">
                                <small style="font-size: 10px !important;">Notify</small>
                            </button>
                        
                    </p>
                    @if (!empty($item->smsNotifications))
                        @php
                            $smsNotifications = json_decode($item->smsNotifications, true);
                        @endphp
                        <i class="text-gray-500" style="font-size: 10px !important;">
                            {{ $smsNotifications[0]['status'] ?? 'No status available' }}
                        </i>
                    @else
                        <i class="text-gray-500" style="font-size: 10px !important;">No notifications</i>
                    @endif
                    </div>
                    
                </div>
            </div>
            </form>
            @endforeach
        </div>
    </x-filament::modal>
</div>