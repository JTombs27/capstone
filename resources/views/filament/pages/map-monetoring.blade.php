
<div class="grid grid-cols-12 mt-5">
     <div class="md:col-span-8 lg:col-span-8 col-span-12" wire:ignore>
            <div class="shadow p-4 bg-slate-900" style="border-radius: 10px 0px 0px 0px;">
              <h5 class="font-bold text-gray-700 text-white">
                        Geographic Information System
              </h5>
           </div>
            <div class="shadow bg-slate-900 z-10"  id="map" style="height: 508px; width: 100%;"></div>
        <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
   
        <script>
            document.addEventListener('DOMContentLoaded', function () 
            {
                

                //google streets
                var googleStreets = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{
                    maxZoom: 15,
                    minZoom: 7,  
                    subdomains:['mt0','mt1','mt2','mt3']
                });

                //map initialization

                //fetch data
                var data = @json($this->getFarms());
                
                var allFarmType = L.layerGroup();
                var baboyan = L.layerGroup();
                var manokan = L.layerGroup();

                // Arrays to store diseaseMarker and circle layers
                const diseaseLayers = L.layerGroup();

                // // Add farm markers
                data.forEach(function (farm) {

                    var iconUrl = '/images/farmMarker.png'
                    if(farm.animal_name != "All")
                    {
                        iconUrl = '/images/'+farm.animal_name+'.png';
                    }


                    var marker = L.marker([parseFloat(farm.latitude), parseFloat(farm.longitude)], {
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
                
                var map = L.map('map', {
                    center: [7.5547,126.1404],
                    zoom: 9,
                    layers: [googleStreets,allFarmType]
                });
                
                var monitoredDiseases = @json($this->getDiseaseMonitored());

                // Add Disease Markers and Circles
                monitoredDiseases.forEach(function (disease) 
                {
                    const customIcon = L.icon({
                        iconUrl: '/images/diseaseMarker.png', // Custom icon image URL
                        iconSize: [41, 41],
                        iconAnchor: [12, 40],
                        popupAnchor: [1, -34]
                    })
                    const circle = L.circle([disease.latitude, disease.longitude], {
                        color: 'red',       // Circle border color
                        fillColor: 'green', // Circle fill color
                        fillOpacity: 0.5,   // Circle fill opacity
                        radius: 100,       // Circle radius in meters
                        weight: 4,
                        opacity: 0.4
                    })
                    .addTo(map);


                     circle.on('mouseover', function (e) {
                        // Show details in a tooltip
                        circle.bindTooltip('Disease : ' + disease.disease.disease_description, {
                            permanent: false,
                            direction: 'top',
                        }).openTooltip(e.latlng);

                    });

                                            // Mouseout event: Reset the style and remove details
                    circle.on('mouseout', function (e) {
                            e.target.closeTooltip();
                        });

                    var params = {
                        "details": disease
                    };


                    circle.on('click', function () {
                             window.dispatchEvent(new CustomEvent('open-disease-modal', { detail: params }));
                            
                        });

                    diseaseLayers.addLayer(circle);

                });

                var overlayMaps = {
                    "All Farms": allFarmType,
                    "Baboyan": baboyan,
                    "Manokan": manokan
                };

                        fetch('/geoJSON/BarangayBoundary.json')
                        .then(response => response.json())
                        .then(geojsonData => {
                            
                            const barangayColors = ["red", "transparent"]; // Blinking colors
                            let currentIndexB = 0;
                            var geoJsonLayerB= L.geoJSON(geojsonData, {
                                style: function (feature) {
                                    return { color: "white", weight: 1,fillOpacity:0.5,fillColor:"gray"};
                                },
                                onEachFeature: function (feature, layer) {
                                    if (feature.properties && feature.properties.Brgy) 
                                    {
                                        if (feature.properties.Brgy.toLowerCase() == "casson vs kidawa" || feature.properties.Brgy.toLowerCase() == "casoon") {
                                                
                                                layer.blink = true;
                                                layer.addTo(map);
                                            }

                                        layer.bindPopup("Brangay: " + feature.properties.Brgy+" Municipality:"+feature.properties.MUN);
                                        // Handle click on the polygon
                                            layer.on('dblclick', function (e) {
                                                // Get the clicked coordinates
                                                const clickedCoordinates = e.latlng;
                                                console.log("Polygon clicked at:", clickedCoordinates);

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
                                                    color: 'white',
                                                    weight: 5,
                                                    fillOpacity: 0.4,
                                                    fillColor:"black"
                                                });

                                                // Show details in a tooltip
                                                layer.bindTooltip("Barangay: " + feature.properties.Brgy+"<br/>Municipality:"+feature.properties.MUN, {
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

                            var overLayBorders = {
                                //"Municipalities":geoJsonLayerMunicipality,
                                "Barangays": geoJsonLayerB,
                            };

                             // Set up the blinking effect
                            setInterval(() => {
                                currentIndexB = (currentIndexB + 1) % barangayColors.length;

                                geoJsonLayerB.eachLayer(layer => {
                                    // Check if this layer should blink
                                    if (layer.blink) {
                                        layer.setStyle({
                                            color: barangayColors[currentIndexB],
                                            fillOpacity:1
                                        });
                                    }
                                });
                            }, 500); // Blink every 1 second

                            // Add the layer control to the map
                            var layerControl = L.control.layers(overlayMaps, overLayBorders,{
                                collapsed:false
                            });
                            layerControl.addTo(map);

                            //  document.getElementById('opacityInput').addEventListener('input', function (event) {
                            //         // if (event.key === 'Enter') { // Trigger on "Enter" key press
                            //             const opacityValue = parseFloat(this.value); // Get the value from the input field
                            //             if (!isNaN(opacityValue) && opacityValue >= 0 && opacityValue <= 1) {
                            //                 currentOpacity = opacityValue;
                            //                 console.log("Updated fillOpacity to:", currentOpacity); // Log the new opacity value
                            //             } else {
                                            
                            //             }
                            //         // }
                            //     });
                            
                        })
                        .catch(error => {
                            console.error("Error loading GeoJSON:", error);
                        });
                

                map.on('dblclick', function (e) 
                    {
                        
                    });
                            
            });

        
        </script>
</div>
<div class="md:col-span-4 lg:col-span-4 col-span-12 ">
    <div class=" p-4 bg-slate-900" style="border-radius: 0px 10px 0px 0px;">
        <label class="font-bold text-center text-gray-700 text-white col-span-8">Map Settings</label>
    </div>
     <div class=" text-white pl-3 pr-3 pb-2 bg-slate-900 h-[38px]" ><label class="col-span-12 text-sm text-bold">Municipal Boundaries</label></div>
    {{-- <div class=" text-white pl-3 pr-3 pb-2 bg-slate-900 h-[158px]" >
         <label class="col-span-12 text-sm text-bold">Municipal Boundaries</label>
         <hr>
         <div class="grid grid-cols-12 gap-y-3">
             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="blinking-svg size-6 mt-2 container my-auto size-6 fill-marker-danger stroke-marker-danger col-span-1">
                <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
            </svg>
            <label class="col-span-2 text-sm container my-auto">Red</label>
            <label class="col-span-1 text-sm container my-auto">=</label>
            <label class="col-span-8 text-sm container my-auto">Infected Zone.</label>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="blinking-svg-pink size-6  container my-auto size-6 fill-marker-warnig stroke-marker-warnig col-span-1">
                <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
            </svg>
            <label class="col-span-2 text-sm container my-auto">Pink </label>
            <label class="col-span-1 text-sm container my-auto">=</label>
            <label class="col-span-8 text-sm container my-auto">Buffer zone (ASF Free).</label>
             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="blinking-svg-yellow size-6  container my-auto size-6 fill-marker-warnig stroke-marker-warnig col-span-1">
                <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
            </svg>
            <label class="col-span-2 text-sm container my-auto">Yellow</label>
            <label class="col-span-1 text-sm container my-auto">=</label>
            <label class="col-span-8 text-sm container my-auto">Surviellance zone (ASF Free).</label>
         </div>
        
    </div> --}}
    <div class="shadow bg-slate-900 z-10" wire:ignore  id="map2" style="height: 470px; width: 100%;"></div>
     <script>
            document.addEventListener('DOMContentLoaded', function () 
            {
                

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
   
</div>
<div class="md:col-span-12 lg:col-span-12 col-span-12" style="margin-top: 0px;">
     <div class=" p-4 bg-slate-800">
        <div class="grid grid-cols-12">
            <div class="col-span-12">
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
                                    {{-- <i class="text-gray-500" style="font-size: 10px !important;">{{!empty($item->smsNotifications) ? $item->smsNotifications:"Not yet notified!" }}</i> --}}
                                     {{-- <i class="text-gray-500" style="font-size: 10px !important;">{{$item->smsNotifications }}</i> --}}
                                    </div>
                                    
                                </div>
                            </div>
                            </form>
                         @endforeach
                        </div>
                         
                    </x-filament::modal>

                {{-- <x-filament::page></x-filament::page> --}}
            </div>
        </div>
     </div>
</div>
</div>