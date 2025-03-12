
<div class="grid grid-cols-12 mt-5">
     <div class="md:col-span-8 lg:col-span-8 col-span-12">
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
                monitoredDiseases.forEach(function (disease) {
                    const customIcon = L.icon({
                        iconUrl: '/images/diseaseMarker.png', // Custom icon image URL
                        iconSize: [41, 41],
                        iconAnchor: [12, 40],
                        popupAnchor: [1, -34]
                    });

                    // Create the marker
                    // const diseaseMarker = L.marker([disease.latitude, disease.longitude], { icon: customIcon })
                    //     .bindPopup('Disease : ' + disease.disease.disease_description)
                    //     .addTo(map);

                    // Create the circle
                    const circle = L.circle([disease.latitude, disease.longitude], {
                        color: 'red',       // Circle border color
                        fillColor: 'green', // Circle fill color
                        fillOpacity: 0.5,   // Circle fill opacity
                        radius: 100,       // Circle radius in meters
                        weight: 4,
                        opacity: 0.4
                    })
                        .bindPopup('Disease : ' + disease.disease.disease_description)
                        .addTo(map);

                    // Add layers to the diseaseLayers array
                    //(diseaseMarker, 
                    diseaseLayers.addLayer(circle);

                });

                var overlayMaps = {
                    "All Farms": allFarmType,
                    "Baboyan": baboyan,
                     "Manokan": manokan
                };

                var municipalities = @json($this->getMunicipalities());
                
                var geoJsonLayerMunicipality;
                var currentOpacity = document.getElementById('opacityInput').value;
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
                                     
                                    return { color: color,stroke:true, weight: 2, fillOpacity:currentOpacity, opacity:1};
                                },
                                onEachFeature: function (feature, layer) {
                                    if (feature.properties && feature.properties.MUN) 
                                    {
                                       
                                        if(feature.properties.MUN.toLowerCase() == "new bataan")
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
                            }) //.addTo(map);

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
                                "Municipalities":geoJsonLayerMunicipality,
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

                             document.getElementById('opacityInput').addEventListener('input', function (event) {
                                    // if (event.key === 'Enter') { // Trigger on "Enter" key press
                                        const opacityValue = parseFloat(this.value); // Get the value from the input field
                                        if (!isNaN(opacityValue) && opacityValue >= 0 && opacityValue <= 1) {
                                            currentOpacity = opacityValue; // Update the current opacity
                                            geoJsonLayerMunicipality.eachLayer(layer => {
                                                // Apply the new opacity to all layers
                                                layer.setStyle({
                                                    fillOpacity: currentOpacity
                                                });
                                            });
                                            console.log("Updated fillOpacity to:", currentOpacity); // Log the new opacity value
                                        } else {
                                            
                                        }
                                    // }
                                });
                            
                        })
                        .catch(error => {
                            console.error("Error loading GeoJSON:", error);
                        });
                

                map.on('dblclick', function (e) {
                        // Log or display the coordinates where the map was clicked
                        console.log("Clicked Coordinates:", e.latlng);
                        L.marker(e.latlng).addTo(map);
                    });
                            
            });

        
        </script>
</div>
<div class="md:col-span-4 lg:col-span-4 col-span-12 ">
    <div class=" p-4 bg-slate-900" style="border-radius: 0px 10px 0px 0px;">
        <label class="font-bold text-center text-gray-700 text-white col-span-8">Map Settings</label>
    </div>
    <div class=" text-white pl-3 pr-3 pb-2 bg-slate-900 h-[508px]"> 
        <div class="grid grid-cols-12 gap-y-3">
             <div class="col-span-full grid grid-cols-12 border rounded p-3">
                <div class="col-span-4 container my-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-55">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="col-span-8 container my-auto">
                        <label id="dateCal" class="text-2xl font-bold text-slate-50">
                        </label><br>
                        <label id="clock" class="text-xl font-semibold text-blue-500 "></label><br>
                        <label id="week" class="text-2xl font-semibold text-blue-500 "></label>
                </div>
            </div>
             <hr class="col-span-12">
            <label class="col-span-12 text-sm text-bold">Municipality</label>
            <label class="container col-span-7 my-auto text-sm">Map Opacity:</label>
            <input type="number" class="col-span-5 rounded-lg text-black border text-xs" id="opacityInput" value="0" placeholder="Enter opacity (0-1)" min="0" max="1" step="0.1">
            <hr class="col-span-12">
            <label class="col-span-12">Indicators</label>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class=" container my-auto size-6 fill-farm-marker stroke-slate-50 col-span-3">
            <path fill-rule="evenodd" d="m11.54 22.351.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 0 0-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
            </svg>
            <label class="col-span-9 text-sm container my-auto">Registered Farms with farm type.</label>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class=" container my-auto size-6 fill-marker-danger stroke-slate-50 col-span-3">
                <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
            </svg>
            <label class="col-span-9 text-sm container my-auto">Disease Marker indicators and its scope.</label>

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="blinking-svg size-6 container my-auto size-6 fill-marker-danger stroke-marker-danger col-span-3">
                <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
            </svg>
            <label class="col-span-9 text-sm container my-auto">Blinking red in map, Barangay or Municipal report level threshold have reach.</label>
           
            <script>
                function updateClock() {
                    const clockElement = document.getElementById('clock');
                    const labelDate = document.getElementById('dateCal');
                    const weeklbl = document.getElementById('week');
                    const now = new Date();
                    const hours = now.getHours().toString().padStart(2, '0') == "00" ? "12":now.getHours().toString().padStart(2, '0');
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const seconds = now.getSeconds().toString().padStart(2, '0');
                    const ampm = now.getHours().toString().padStart(2, '0') >= 12 ? 'PM' : 'AM';
                    const options = {
                                weekday: 'long',    // Day of the week (e.g., "Sunday")
                                year: 'numeric',    // Full year (e.g., 2025)
                                month: 'short',     // Abbreviated month (e.g., "Jan")
                                day: '2-digit',     // Day as 2 digits (e.g., "12")
                            };

                            // Format the date
                    const formattedDate = now.toLocaleDateString('en-US', options);

                            // Modify the month to be uppercase
                    const [weekday, month, day, year] = formattedDate.split(' ');
                    dateCal.innerHTML = `${(hours >12 ? (hours-12).toString().padStart(2, '0'):hours)}:${minutes}:${seconds} ${ampm}`;
                    clockElement.textContent = `${month.toUpperCase()}. ${day} ${year}`;
                    weeklbl.textContent = `${weekday.replace(',','')}`;
                }

                setInterval(updateClock, 1000);
                // updateClock();
            </script>

        </div>
        {{-- dark:bg-cyan-300 --}}
        {{-- <x-filament::page></x-filament::page> --}}
    </div>
</div>
<div class="md:col-span-12 lg:col-span-12 col-span-12" style="margin-top: 0px;">
     <div class=" p-4 bg-slate-800">
        <div class="grid grid-cols-12">
            <div class="col-span-12">
                <x-filament::page></x-filament::page>
            </div>
        </div>
     </div>
</div>
</div>