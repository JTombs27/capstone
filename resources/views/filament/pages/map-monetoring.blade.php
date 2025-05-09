
@php
    $currentYear = date('Y');
    $years = range($currentYear, 1900);
@endphp

<div class="grid grid-cols-12 relative overflow-hidden">
     <style>
        .fi-main { padding: 0 !important;}
    </style>
     <div class="md:col-span-12 lg:col-span-12 col-span-12" wire:ignore>
        {{-- <div class="shadow p-x-4 p-y-2 bg-slate-900" style="border-radius: 10px 10px 0px 0px;">
            <h5 class="font-bold text-gray-700 text-white">
                    Geographic Information System
            </h5>
        </div> --}}
        <div class="shadow bg-slate-900 z-10 col-span-12 grid grid-cols-12 h-svh w-full"  id="map" ></div>

        {{-- <div class="absolute bottom-20 left-4 z-20 bg-white p-4 rounded-lg shadow-lg w-[600px]" style="font-size: 12px;">
            <h4 class="font-bold mb-[2px]">Filters</h4>
            <div>
                <label class="inline mb-1">Year :</label>
                <select class="border-gray-300 rounded px-2 py-1 w-full" style="font-size: 12px;">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="inline mb-1">Select Category:</label>
                <select class="border-gray-300 rounded px-2 py-1 w-full" style="font-size: 12px;">
                    <option value="">All</option>
                    <option value="1">Category 1</option>
                    <option value="2">Category 2</option>
                </select>
            </div>
        </div> --}}
        <div class="absolute top-4 left-4 z-20 bg-white p-4 rounded-lg shadow-lg grid grid-cols-12 w-[500px] gap-x-2" style="font-size: 12px;">
            <h4 class="font-bold mb-[2px] col-span-2  pt-2">Filters</h4>
            <div class="col-span-4 grid grid-cols-12 gap-x-2">
                <label class="col-span-3 inline mb-1 pt-2">Year: </label>
                <div class="col-span-9">
                <select class="border-gray-300 rounded px-2 py-1 w-full" style="font-size: 12px;">
                    <option value="">All</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
                </div>
            </div>
           <div class="col-span-6 grid grid-cols-12 gap-x-2">
                <label class="col-span-5 inline mb-1 pt-2">Municipality: </label>
                <div class="col-span-7">
                <select class="border-gray-300 rounded px-2 py-1 w-full" style="font-size: 12px;">
                    <option value="">All</option>
                     @foreach ($this->municipalities as $municipalities)
                        <option value="{{ $municipalities->municipality_name }}">{{ $municipalities->municipality_name }}</option>
                    @endforeach
                </select>
                </div>
            </div>
        </div>
        <div class="absolute top-[90px] left-4 z-20 bg-white p-4 rounded-lg shadow-lg w-[200px]" style="font-size: 12px;">
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                        </svg>
                        <span style="padding-top:1px;">&nbsp;View Barangays</span>
                        </div>
                        <div class="col-span-1"  style="padding-top:1px;"><input type="checkbox" id="showBarangays"  name="accept_terms" class="form-checkbox text-primary-600"></div>
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
    @push('scripts')
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
        <script>
       
        document.addEventListener('DOMContentLoaded', function () 
            {
                 var googleStreets = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{
                    maxZoom: 15,
                    minZoom: 7,  
                    subdomains:['mt0','mt1','mt2','mt3']
                });
                
                var map = L.map('map', {
                            center: [7.5547,126.1404],
                            zoom: 10,
                            layers: [googleStreets],
                            zoomControl: false 
                        });   
            const heatData = [
                    [7.6071, 126.0005, 10], // Nabunturan
                    [7.3573, 125.8504, 10], // Pantukan
                    [7.3983, 125.9607, 10], // Mabini
                    [7.4300, 126.0900, 10], // Mawab
                    [7.5132, 126.0370, 10], // Maco
                    [7.6713, 126.1420, 10], // New Bataan
                    [7.7432, 126.1221, 10], // Compostela
                    [7.8135, 126.2650, 10], // Montevista
                    [7.8500, 126.3200, 10], // Monkayo
                    [7.9599, 126.0378, 10], // Laak (San Vicente)
                ];


                var heatLayer = L.heatLayer(heatData, {
                        radius: 25,
                        blur: 15,
                        maxZoom: 14
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
                    <table> <tr> <td style="width:40%">  <img class="mx-auto my-auto h-100 block rounded-full"  src="/images/five-farm-biosecurity-practices.jpg" alt="product image" /></tr> </table>
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

                var monitoredDiseases   = @json($this->getDiseaseMonitored());
                var municipalities      = @json($this->getMunicipalities());
                var geoJsonLayerMunicipality;
                var currentOpacity      = 0.2;
                fetch('/geoJson/MunicipalBoundary.json')
                        .then(response => response.json())
                        .then(geojsonData => 
                        {
                           
                            let currentIndex = 0;
                            const Municipalcolors = ["red","transparent"];
                            geoJsonLayerMunicipality = L.geoJSON(geojsonData, {
                                style: function (feature) 
                                {
                                    var municipality = municipalities.find(m =>  m.municipality_name.toLowerCase() == feature.properties.MUN.toLowerCase());
                                    var color = municipality ? municipality.color : 'orange';
                                     
                                    return { 
                                        color: 'gray',
                                        stroke:true, 
                                        weight: 2, 
                                        fillColor:color,
                                        fillOpacity:0};
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
                                                    fillOpacity: 0.2,
                                                });

                                                // Show details in a tooltip
                                                // layer.bindTooltip("Municipality: " + feature.properties.MUN, {
                                                //     permanent: false,
                                                //     direction: 'top',
                                                // }).openTooltip(e.latlng);

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
                            })
                            .addTo(map);

                            
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
                                style: function (feature) 
                                {
                                    var municipality = municipalities.find(m =>  m.municipality_name.toLowerCase() == feature.properties.MUN.toLowerCase());
                                    var color = municipality ? municipality.color : 'orange';
                                     
                                    return { 
                                        color: 'gray',
                                        stroke:true, 
                                        weight: 1, 
                                        fillColor:"gray",
                                        fillOpacity:0.3};
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
                                                    fillOpacity: 0.2,
                                                });

                                                // Show details in a tooltip
                                                layer.bindTooltip("Barangay: " + feature.properties.Brgy.toLowerCase(), {
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
                            })
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
                     if (map.hasLayer(heatLayer)) 
                        {
                            //geoJsonLayerMunicipality.addTo(map);
                             heatLayer.removeFrom(map);
                        }
                    else{
                            //geoJsonLayerMunicipality.removeFrom(map);
                            heatLayer.addTo(map);
                            const canvas                = heatLayer._heat._canvas;
                            canvas.willReadFrequently   = true;
                            heatData.forEach(point => 
                            {
                                L.circleMarker([point[0], point[1]], {
                                    radius: 10,
                                    color: 'transparent',
                                    fillOpacity: 0
                                })
                                .on('click', () => {
                                    window.dispatchEvent(new CustomEvent('open-disease-modal', { detail: 4 }));
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
                        geoJsonLayerMunicipality.addTo(map);
                    }
                    else
                    {
                        geoJsonLayerB.addTo(map);
                        geoJsonLayerMunicipality.removeFrom(map);
                    }
                }

                document.getElementById('showHeatMapButton').addEventListener('click', showDiseaseCaseHeatMap);
                document.getElementById('showBarangays').addEventListener('click', showBarangays);
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
                showDiseaseCaseHeatMap();
                document.getElementById('showHeatMapButton').checked = true;

        });
        
      
    </script>
    @endpush
</div>
{{-- <div class="md:col-span-4 lg:col-span-4 col-span-12 ">
    <div class=" p-4 bg-slate-900" style="border-radius: 0px 10px 0px 0px;">
        <label class="font-bold text-center text-gray-700 text-white col-span-8">Map Settings</label>
    </div>
     <div class=" text-white pl-3 pr-3 pb-2 bg-slate-900 h-[38px]" ><label class="col-span-12 text-sm text-bold">Municipal Boundaries</label></div>
    
    <div class="shadow bg-slate-900 z-10" wire:ignore  id="map2" style="height: 470px; width: 100%;"></div>
     <script>
            document.addEventListener('DOMContentLoaded', function () 
            {
              const heatData = [
                    [7.6071, 126.0005, 0.6], // Nabunturan
                    [7.3573, 125.8504, 0.7], // Pantukan
                    [7.3983, 125.9607, 0.8], // Mabini
                    [7.4300, 126.0900, 0.4], // Mawab
                    [7.5132, 126.0370, 0.9], // Maco
                    [7.6713, 126.1420, 0.5], // New Bataan
                    [7.7432, 126.1221, 0.3], // Compostela
                    [7.8135, 126.2650, 0.7], // Montevista
                    [7.8500, 126.3200, 0.6], // Monkayo
                    [7.9599, 126.4000, 0.8], // Laak (San Vicente)
                ];
               // Heatmap data
                const heatmapData = {
                    max: 8,
                    data: [
                        { lat:7.6071,  lng: 126.0005, value: 5},
                        { lat:7.3573,  lng: 125.8504, value: 6},
                        { lat:7.3983,  lng: 125.9607, value: 4},
                        { lat:7.4300,  lng: 126.0900, value: 5},
                        { lat:7.5132,  lng: 126.0370, value: 6},
                        { lat:7.6713,  lng: 126.1420, value: 4},
                        { lat:7.7432,  lng: 126.1221, value: 5},
                        { lat:7.8135,  lng: 126.2650, value: 6},
                        { lat:7.8500,  lng: 126.3200, value: 4},
                        { lat:7.9599,  lng: 126.4000, value: 4},
                    ]
                };

                const cfg = {
                    radius: 40,
                    maxOpacity: .8,
                    scaleRadius: true,
                    useLocalExtrema: true,
                    latField: 'lat',
                    lngField: 'lng',
                    valueField: 'value'
                };

                const heatmapLayer = new HeatmapOverlay(cfg);
                map.addLayer(heatmapLayer);
                heatmapLayer.setData(heatmapData);
                  // Add Disease Markers and Circles
                // monitoredDiseases.forEach(function (disease) 
                // {
                //     const customIcon = L.icon({
                //         iconUrl: '/images/diseaseMarker.png', // Custom icon image URL
                //         iconSize: [41, 41],
                //         iconAnchor: [12, 40],
                //         popupAnchor: [1, -34]
                //     })
                //     const circle = L.circle([disease.latitude, disease.longitude], {
                //         color: 'red',       // Circle border color
                //         fillColor: 'green', // Circle fill color
                //         fillOpacity: 0.5,   // Circle fill opacity
                //         radius: 100,       // Circle radius in meters
                //         weight: 4,
                //         opacity: 0.4
                //     })
                //     .addTo(map);


                //      circle.on('mouseover', function (e) {
                //         // Show details in a tooltip
                //         circle.bindTooltip('Disease : ' + disease.disease.disease_description, {
                //             permanent: false,
                //             direction: 'top',
                //         }).openTooltip(e.latlng);

                //     });

                //                             // Mouseout event: Reset the style and remove details
                //     circle.on('mouseout', function (e) {
                //             e.target.closeTooltip();
                //         });

                //     var params = {
                //         "details": disease
                //     };


                //     circle.on('click', function () {
                //              window.dispatchEvent(new CustomEvent('open-disease-modal', { detail: params }));
                            
                //         });

                //     diseaseLayers.addLayer(circle);

                // });

                // var overlayMaps = {
                //     "All Farms": allFarmType,
                //     "Baboyan": baboyan,
                //     "Manokan": manokan
                // };

                // fetch('/geoJson/BarangayBoundary.json')
                //         .then(response => response.json())
                //         .then(geojsonData => 
                //         {
                            
                //             const barangayColors = ["red", "transparent"]; // Blinking colors
                //             let currentIndexB = 0;
                //             var geoJsonLayerB= L.geoJSON(geojsonData, {
                //                 style: function (feature) {
                //                     return { color: "white", weight: 1,fillOpacity:0.5,fillColor:"gray"};
                //                 },
                //                 onEachFeature: function (feature, layer) {
                //                     if (feature.properties && feature.properties.Brgy) 
                //                     {
                //                         if (feature.properties.Brgy.toLowerCase() == "casson vs kidawa" || feature.properties.Brgy.toLowerCase() == "casoon") {
                                                
                //                                 layer.blink = true;
                //                                 layer.addTo(map);
                //                             }

                //                         layer.bindPopup("Brangay: " + feature.properties.Brgy+" Municipality:"+feature.properties.MUN);
                //                         // Handle click on the polygon
                //                             layer.on('dblclick', function (e) {
                //                                 // Get the clicked coordinates
                //                                 const clickedCoordinates = e.latlng;
                //                                 console.log("Polygon clicked at:", clickedCoordinates);

                //                                 // Trigger the map's click handler manually
                //                                 map.fire('dblclick', {
                //                                     latlng: clickedCoordinates,
                //                                     layerPoint: e.layerPoint,
                //                                     containerPoint: e.containerPoint,
                //                                     originalEvent: e.originalEvent
                //                                 });

                //                                 // Optional: Prevent propagation to other events
                //                                 L.DomEvent.stopPropagation(e);
                //                             });

                //                            // Mouseover event: Highlight the polygon and show details
                //                             layer.on('mouseover', function (e) {
                //                                 // Highlight the polygon
                //                                 e.target.setStyle({
                //                                     color: 'white',
                //                                     weight: 5,
                //                                     fillOpacity: 0.4,
                //                                     fillColor:"black"
                //                                 });

                //                                 // Show details in a tooltip
                //                                 layer.bindTooltip("Barangay: " + feature.properties.Brgy+"<br/>Municipality:"+feature.properties.MUN, {
                //                                     permanent: false,
                //                                     direction: 'top',
                //                                 }).openTooltip(e.latlng);

                //                             });

                //                             // Mouseout event: Reset the style and remove details
                //                             layer.on('mouseout', function (e) {
                //                                 // Reset the style to default
                //                                 geoJsonLayerB.resetStyle(e.target);

                //                                 // Remove the tooltip
                //                                 e.target.closeTooltip();
                //                             });
                //                     }
                //                 }
                //             });

                //             var overLayBorders = {
                //                 "Barangays": geoJsonLayerB,
                //             };

                //              // Set up the blinking effect
                //             setInterval(() => {
                //                 currentIndexB = (currentIndexB + 1) % barangayColors.length;

                //                 geoJsonLayerB.eachLayer(layer => {
                //                     if (layer.blink) {
                //                         layer.setStyle({
                //                             color: barangayColors[currentIndexB],
                //                             fillOpacity:1
                //                         });
                //                     }
                //                 });
                //             }, 500); // Blink every 1 second

                //             // Add the layer control to the map
                //             // var layerControl = L.control.layers(overlayMaps, overLayBorders,{
                //             //     collapsed:false
                //             // });
                //             // layerControl.addTo(map);
                            
                //         })
                //         .catch(error => {
                //             console.error("Error loading GeoJSON:", error);
                //         });
                

                // map.on('dblclick', function (e) 
                //     {
                        
                //     });
                            

                //google streets 'http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}' 
                var googleStreets2 = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}',{
                    maxZoom: 9,
                    minZoom: 9,  
                    subdomains:['mt0','mt1','mt2','mt3']
                });

                var map2 = L.map('map2', {
                    center: [7.5547,126.1404],
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
{{-- <div class="md:col-span-12 lg:col-span-12 col-span-12" style="margin-top: 0px;">
     <div class=" p-4 bg-slate-800">
        <div class="grid grid-cols-12">
            <div class="col-span-12">
            </div>
        </div>
     </div>
</div> --}}
<x-filament::modal id="diseaseInfo" slide-over class="z-[999]" alignment="center" width="md">
    <x-slot name="heading">
        Disease Information
    </x-slot>
        Municipality of: {{ $selectedDisease["municipal"]['municipality_name'] ?? '' }}<br/>
        Under Barangay of: {{ $selectedDisease["barangay"]['barangay_name'] ?? '' }}<br/>
        <p><b>Disease :</b>{{ $selectedDisease["disease"]['disease_description'] ?? '' }} 
        <i class="text-gray-500" style="font-size: 12px !important;">Sakit sa {{ $selectedDisease["animal"]['animal_name'] ?? ''}}</i>
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