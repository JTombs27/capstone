
@php
    $currentYear = date('Y');
    $years = range($currentYear, 1900);
@endphp

<div class="grid grid-cols-12 relative overflow-hidden">
     <style>
        .fi-main { padding: 0 !important;}
    </style>
     <div class="md:col-span-12 lg:col-span-12 col-span-12" wire:ignore>
        <div class="absolute left-4 z-20 bg-white p-4 rounded-lg shadow-lg grid grid-cols-12 w-[700px] gap-x-2" style="font-size: 12px; top:5px !important;">
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
                <select id="filter_municipality" class="border-gray-300 rounded px-2 py-1 w-full" style="font-size: 12px;">
                    <option value="">All</option>
                     @foreach ($this->municipalities as $municipalities)
                        <option value="{{ $municipalities->municipality_name }}">{{ $municipalities->municipality_name }}</option>
                    @endforeach
                </select>
                </div>
            </div>
        </div>
        <div class="absolute top-[78px] left-4 z-20 bg-white p-4 rounded-lg shadow-lg w-[200px]" style="font-size: 12px;">
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
        <div class="shadow bg-slate-900 z-10 col-span-12 grid grid-cols-12 w-full" style="height: calc(100vh - 64px);"  id="map" ></div>
        
    @push('scripts')
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
        <script>
        let geoJsonLayerMunicipality;
        document.addEventListener('DOMContentLoaded', function () 
            {
                const currentYear = new Date().getFullYear();
                document.getElementById('filter_year_from').addEventListener('change', showDiseaseCaseHeatMap);
                document.getElementById('filter_year_from').value   = 2000;
                document.getElementById('filter_year_to').value     = currentYear;
                
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
                map.createPane('polygonsPane');
                map.getPane('polygonsPane').style.zIndex = 600;

                map.createPane('heatPane');
                map.getPane('heatPane').style.zIndex = 700;

                map.createPane('markerPane'); // optional; markers use default pane
                map.getPane('markerPane').style.zIndex = 750;

                var monitoredDiseases   = @json($this->getDiseaseMonitored(2000,2025));    
                const heatPoints = monitoredDiseases.map(item => [item.latitude, item.longitude, item.affected_count]);

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
                var year_from           = document.getElementById('filter_year_from').value;
                var year_to             = document.getElementById('filter_year_to').value;   
               
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
                                    var color = "ac6516"//municipality ? municipality.color : 'orange';
                                     
                                    return { 
                                        color: '#ac6516',
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
                                                    color: '#f9a547',
                                                    weight: 3,
                                                    fillOpacity: 0.3,
                                                    fillColor:"#f9a547"
                                                });
                                            });

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

                            showDiseaseCaseHeatMap();
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
                                        fillOpacity:0.3};
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
                            });
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
                            geoJsonLayerMunicipality.removeFrom(map);
                            heatLayer.removeFrom(map);
                        }
                    else{
                        if(!document.getElementById('showBarangays').checked)
                        {
                            geoJsonLayerMunicipality.addTo(map);
                        }
                           map.removeLayer(heatLayer);
                           map.addLayer(heatLayer);
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
                        geoJsonLayerMunicipality.addTo(map);
                    }
                    else
                    {
                        geoJsonLayerB.addTo(map);
                        geoJsonLayerMunicipality.removeFrom(map);
                    }

                    showDiseaseCaseHeatMap();
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
                    //console.log(filtered);

                    renderHeatmap(filtered);
                }


                function renderHeatmap(filteredData) 
                {
                    const heatPoints = filteredData.map(item => [
                        item.latitude,
                        item.longitude,
                        item["affected_count"]
                    ]);
                
                    // Remove old heat layer if it exists
                    if (heatLayer) {
                        map.removeLayer(heatLayer);
                    }
                
                    // Create new heat layer
                    heatLayer = L.heatLayer(heatPoints, {
                        radius: 25,
                        blur: 10,
                        maxZoom: 17,
                        pane: 'heatPane'
                    }).addTo(map);

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

                document.getElementById('showHeatMapButton').addEventListener('click', showDiseaseCaseHeatMap);
                document.getElementById('showBarangays').addEventListener('click', showBarangays);
                document.getElementById('filter_year_from').addEventListener('change', filterByDiseaseType);
                document.getElementById('filter_year_to').addEventListener('change', filterByDiseaseType);
                document.getElementById('filter_municipality').addEventListener('change', filterByDiseaseType);

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