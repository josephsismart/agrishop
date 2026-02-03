<script>
    // Initialize map
    let userMarker = null;
    let userLatLng = null;
    let routingControl = null;
    let searchedFarms = [];
    let routes = [];
    let manualMarker = null;
    let manualLocationEnabled = false;



    var map = L.map('map').setView([8.85, 125.65], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Ensure proper sizing
    setTimeout(() => map.invalidateSize(), 0);
    window.addEventListener('resize', () => map.invalidateSize());
    let mapMarkers = [];
    let markerCoords = [];

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);


    const mapControlsEl = document.getElementById("mapControls");

    L.DomEvent.disableClickPropagation(mapControlsEl);
    L.DomEvent.disableScrollPropagation(mapControlsEl);

    $("#mapControls").on("click touchstart mousedown", function(e) {
        e.stopPropagation();
    });

    $("#toggleControls").on("click", function() {
        const body = $("#mapControlsBody");

        body.slideToggle(200);

        $(this).text(body.is(":visible") ? "–" : "+");
    });

    $("#btnLocateMe").on("click", function() {
        if (!navigator.geolocation) {
            alert("Geolocation not supported by your browser");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                userLatLng = L.latLng(pos.coords.latitude, pos.coords.longitude);

                if (userMarker) {
                    map.removeLayer(userMarker);
                }

                userMarker = L.marker(userLatLng, {
                    icon: L.icon({
                        iconUrl: "https://cdn-icons-png.flaticon.com/512/684/684908.png",
                        iconSize: [30, 30]
                    })
                }).addTo(map).bindPopup("You are here").openPopup();

                map.setView(userLatLng, 13);
            },
            function() {
                alert("Unable to get your location");
            }
        );
    });


    function buildRoutesTable() {
        if (!userLatLng) {
            alert("Please locate yourself first");
            return;
        }

        if (!searchedFarms || searchedFarms.length === 0) {
            alert("No farms to route");
            return;
        }

        let routes = [];

        searchedFarms.forEach(farm => {
            const dist = getDistanceKm(userLatLng, {
                lat: parseFloat(farm.lat),
                lng: parseFloat(farm.lon)
            });

            routes.push({
                id: farm.id,
                name: farm.farm_name,
                lat: farm.lat,
                lon: farm.lon,
                distance: dist
            });
        });

        // sort by distance ASC
        routes.sort((a, b) => a.distance - b.distance);

        renderRoutesTable(routes);
    }


    function renderRoutesTable(routes) {
        const tbody = $("#routesTableBody");
        tbody.empty();

        const minDist = routes[0].distance;
        const maxDist = routes[routes.length - 1].distance;

        routes.forEach((r, i) => {
            let rowClass = "";

            if (r.distance === minDist) rowClass = "table-success";
            if (r.distance === maxDist) rowClass = "table-danger";

            tbody.append(`
            <tr class="${rowClass}">
                <td>${i + 1}</td>
                <td>${r.name}<br>
                        <button class="btn btn-xs btn-primary"
                        onclick="routeToFarm(${r.lat}, ${r.lon}, ${r.id})">
                        View Route
                    </button></td>
                <td>${r.distance.toFixed(2)}</td>
            </tr>
        `);
        });

        $("#routesContainer").slideDown(300);
    }

    function enableManualLocation() {

        // default center of map
        const center = map.getCenter();

        if (!manualMarker) {
            manualMarker = L.marker(center, {
                draggable: true,
                icon: L.icon({
                    iconUrl: "https://cdn-icons-png.flaticon.com/512/684/684908.png",
                    iconSize: [32, 32]
                })
            }).addTo(map);
        } else {
            manualMarker.setLatLng(center);
        }

        manualMarker.bindPopup("📍 Drag this pin to set your location")
            .openPopup();

        userLatLng = manualMarker.getLatLng();

        // When user drags marker
        manualMarker.on("dragend", function(e) {
            userLatLng = e.target.getLatLng();

            $("#distanceInfo").html(
                `📍 Location set: ${userLatLng.lat.toFixed(5)}, ${userLatLng.lng.toFixed(5)}`
            );

            // Optional: clear route when location changes
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            $("#btnViewRoutes").click();
        });
    }


    $("#btnSetManualLocation").on("click", function() {
        manualLocationEnabled = true;
        enableManualLocation();
    });


    map.on("click", function(e) {
        if (!manualMarker || !manualLocationEnabled) return;

        manualMarker.setLatLng(e.latlng);
        userLatLng = e.latlng;

        $("#distanceInfo").html(
            `📍 Location set: ${e.latlng.lat.toFixed(5)}, ${e.latlng.lng.toFixed(5)}`
        );
        $("#btnViewRoutes").click();
    });

    function gpsFailedFallback() {
        alert("GPS unavailable. Please set your location manually.");
        enableManualLocation();
    }

    function resetMapPinsAndRoutes() {

        clearMarkers();
        $("#routesTableBody").empty();
        $("#routesContainer").slideUp(300);
        // 1️⃣ REMOVE ROUTING (Leaflet Routing Machine)
        if (routingControl) {
            map.removeControl(routingControl);
            routingControl = null;
        }

        // 2️⃣ REMOVE MANUAL LOCATION MARKER
        if (manualMarker) {
            map.removeLayer(manualMarker);
            manualMarker = null;
        }

        // 3️⃣ REMOVE USER GPS MARKER
        if (userMarker) {
            map.removeLayer(userMarker);
            userMarker = null;
        }

        // REMOVE ALL ROUTES / POLYLINES
        routes.forEach(route => {
            map.removeLayer(route);
        });
        routes = [];

        // OPTIONAL: reset state vars
        manualMarker = null;
        userLatLng = null;
        manualLocationEnabled = false;

        console.log("🧹 Map reset: pins & routes cleared");
    }


    function routeToFarm(farmLat, farmLon, farmId = null) {
        $("#toggleControls").click();
        $("#toggleControls").text("+");
        $("#routesTableBody tr").removeClass("route-active");
        $(`#eta-${farmId}`).closest("tr").addClass("route-active");
        if (!userLatLng) return;

        if (routingControl) {
            map.removeControl(routingControl);
        }

        routingControl = L.Routing.control({
            waypoints: [
                userLatLng,
                L.latLng(farmLat, farmLon)
            ],
            routeWhileDragging: false,
            addWaypoints: false,
            draggableWaypoints: false,
            show: false,

            lineOptions: {
                styles: [{
                    color: "#1933f7ff", // YELLOW GOLD
                    weight: 7,
                    opacity: 0.9
                }]
            },

            createMarker: () => null // hides default A/B markers (cleaner)
        }).addTo(map);

        routingControl.on('routesfound', function(e) {
            const route = e.routes[0];

            const km = (route.summary.totalDistance / 1000).toFixed(2);
            const min = Math.round(route.summary.totalTime / 60);

            $("#distanceInfo").html(
                `📏 ${km} km | ⏱️ ${min} mins`
            );

            if (farmId) {
                $(`#eta-${farmId}`).text(`${min} mins`);
            }

            // 🔥 AUTO ZOOM TO FULL ROUTE
            // map.fitBounds(L.latLngBounds(route.coordinates), {
            //     padding: [60, 60]
            // });
            map.flyToBounds(L.latLngBounds(route.coordinates), {
                padding: [60, 60],
                duration: 1.2
            });
        });
    }

    $("#btnViewRoutes").on("click", function() {
        buildRoutesTable();
    });


    // Function to compute straight-line distance (Haversine formula)
    function getDistanceKm(latlng1, latlng2) {
        var R = 6371; // km
        var dLat = (latlng2.lat - latlng1.lat) * Math.PI / 180;
        var dLon = (latlng2.lng - latlng1.lng) * Math.PI / 180;
        var a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(latlng1.lat * Math.PI / 180) * Math.cos(latlng2.lat * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }
    [{
        "id": 58,
        "img_path": "dist/img/media/produce/tomato_1764559995.jpg",
        "name": "tomato",
        "harvest_at": "2025-12-01",
        "price": 21,
        "qty_left": 40
    }]

    // Make sure this global mouse tracker runs once (place it near your map init)
    if (!window._leafletMouseTrackerAdded) {
        window._leafletMouseTrackerAdded = true;
        window._leafletLastMouse = {
            x: 0,
            y: 0
        };
        document.addEventListener('mousemove', function(e) {
            window._leafletLastMouse.x = e.clientX;
            window._leafletLastMouse.y = e.clientY;
        });
    }


    function addMarker(farm) {
        var lat = parseFloat(farm.lat);
        var lon = parseFloat(farm.lon);

        var marker = L.marker([lat, lon]).addTo(map);
        mapMarkers.push(marker);
        markerCoords.push([lat, lon]);

        // Parse produce array
        var produceList = [];
        try {
            produceList = JSON.parse(farm.produce);
        } catch (e) {
            console.error("Invalid produce JSON:", farm.produce);
        }

        // Farm image (already HTML from your PHP)
        var farmImgHtml = farm.farm_img_path ? farm.farm_img_path : "";

        // Build produce HTML list with images
        var produceHtml = "";
        if (produceList.length > 0) {
            produceHtml += "<br><b>Produce List:</b><br>";

            produceList.forEach(function(p) {
                var prodImg = p.img_path ?
                    `<img src="${p.img_path}" width="40" height="40" class="rounded">` :
                    `<img src="dist/img/media/icons/1x1.png" width="40" height="40">`;

                produceHtml += `
                <div style="margin-top:6px; border-bottom:1px solid #eee; padding-bottom:4px;">
                    ${prodImg}
                    <b>${p.name}</b> – ₱${p.price} / ${p.uom}<br>
                    <small>Quantity Left: <b>${p.qty_left}</b></small><br>
                    <small>Harvest: <b>${p.harvest_at}</b></small>
                </div>
            `;
            });
        } else {
            produceHtml = "<br><i>No produce available</i>";
        }

        // Marker
        var marker = L.marker([lat, lon]).addTo(map);
        farmCache[farm.id] = farm;
        // Popup with image at the top
        marker.bindPopup(`
                <div style="max-width:240px;">

                    <!-- Farm Image -->
                    <div class="mb-2 text-center">
                        ${farmImgHtml}
                    </div>

                    <!-- Farm Name -->
                    <div class="text-center mb-1">
                        <span class="badge bg-warning text-dark" style="font-size:14px;">
                            ${farm.farm_name}
                        </span>
                    </div>

                    <!-- Location -->
                    <div class="text-center mb-2">
                        <small class="fw-bold">${farm.farm_location}</small><br>
                        <small class="text-muted">
                            Coordinates: ${lat}, ${lon}
                        </small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mb-2">
                        <button class="btn btn-primary btn-sm flex-fill me-1"
                            onclick="orderNow(${farm.id})">
                            Order Now
                        </button>

                        <button class="btn btn-outline-secondary btn-sm flex-fill ms-1"
                            onclick="routeToFarm(${lat}, ${lon})">
                            View Route
                        </button>
                    </div>

                    <hr class="my-2">

                    <!-- Produce List -->
                    <div style="font-size:12px;">
                        ${produceHtml}
                    </div>

                </div>
            `);

        // --- hover / keep-open logic ---
        let isOverMarker = false;
        let isOverPopup = false;
        let closeTimer = null;

        function scheduleClose() {
            // small delay avoids flicker when moving between marker and popup
            clearTimeout(closeTimer);
            closeTimer = setTimeout(() => {
                if (!isOverMarker && !isOverPopup) {
                    marker.closePopup();
                }
            }, 220);
        }



        // marker hover opens popup and cancels any pending close
        marker.on('click', function() { //mouseover
            isOverMarker = true;
            clearTimeout(closeTimer);
            marker.openPopup();
        });

        // marker.on('mouseout', function() {
        //     isOverMarker = false;
        //     scheduleClose();
        // });

        // When ANY popup opens on the map, attach popup hover handlers
        // We check e.popup._source === marker so we only attach for THIS marker's popup.
        function onPopupOpen(e) {
            if (!e.popup || e.popup._source !== marker) return;

            const popupEl = e.popup._container; // DOM element for this popup

            if (userLatLng) {
                const km = getDistanceKm(
                    userLatLng, {
                        lat: lat,
                        lng: lon
                    }
                ).toFixed(2);

                produceHtml += `<br><small>📏 ~${km} km away</small>`;
            }
            // Helper functions for popup mouse tracking
            function popupMouseOver() {
                isOverPopup = true;
                clearTimeout(closeTimer);
            }

            function popupMouseOut() {
                isOverPopup = false;
                scheduleClose();
            }

            // Attach listeners to popup
            popupEl.addEventListener('mouseover', popupMouseOver);
            popupEl.addEventListener('mouseout', popupMouseOut);

            // Also, immediately check if the mouse is already inside the popup (fixes first-hover issue)
            const pos = window._leafletLastMouse;
            if (pos && typeof pos.x === 'number') {
                const elem = document.elementFromPoint(pos.x, pos.y);
                if (elem && popupEl.contains(elem)) {
                    // Mouse is already over the popup when it was opened
                    isOverPopup = true;
                    clearTimeout(closeTimer);
                }
            }

            // Clean up the listeners when this popup closes to avoid leaks
            function onPopupClose(evt) {
                if (!evt.popup || evt.popup._source !== marker) return;
                popupEl.removeEventListener('mouseover', popupMouseOver);
                popupEl.removeEventListener('mouseout', popupMouseOut);
                map.off('popupclose', onPopupClose);
            }
            map.on('popupclose', onPopupClose);

            // We only needed to run this handler once per popup open
        }

        map.on('popupopen', onPopupOpen);

        // optional: remove popupopen listener when marker is removed / if you have cleanup logic
        // --- end hover logic ---

        mapMarkers.push(marker);
        markerCoords.push([lat, lon]);

        if (userLatLng && markerCoords.length > 0) {
            let nearest = null;
            let minDist = Infinity;

            searchedFarms.forEach(farm => {
                const d = getDistanceKm(userLatLng, {
                    lat: parseFloat(farm.lat),
                    lng: parseFloat(farm.lon)
                });

                if (d < minDist) {
                    minDist = d;
                    nearest = farm;
                }
            });

            if (nearest) {
                routeToFarm(nearest.lat, nearest.lon);
            }
        }

    }


    function clearMarkers() {
        for (var i = 0; i < mapMarkers.length; i++) {
            map.removeLayer(mapMarkers[i]);
        }
        mapMarkers = []; // reset array
    }

    function sendCode() {
        let email = 'josephsismart@gmail.com' //$("#email").val();

        $.post("<?= base_url('Signup/email_verification') ?>", {
            action: "send",
            email: email
        }, function(res) {
            let j = JSON.parse(res);
            alert(j.message);
        });
    }

    $("#searchProduce").keypress(function(e) {
        var value = $(this).val();

        if (e.which == 13) {
            e.preventDefault();
            searchProduce();

        }
    })


    function searchProduce() {
        let value = $("#searchProduce").val();
        if (value.length < 3) {
            existAlert("at least 3 characters!");
            return;
        }
        $.post("<?= base_url() ?>" + "userpublicmap/map/searchProduce", {
                value: value
            },
            function(a) {
                var result = JSON.parse(a);
                searchedFarms = result;

                if (result == "") {
                    infoAlert("No results found!");
                }

                if (mapMarkers.length > 0) {
                    resetMapPinsAndRoutes();
                }

                if (result != "") {
                    // FIRST: show map
                    $("#map").fadeIn(1000);

                    // THEN: slide up the landing section
                    $("#landing_Page").slideUp(400);
                    $("#home_click").show();

                    clearMarkers(); // <–– make sure you have this

                    map.invalidateSize();
                    markerCoords = []; // <–– reset coordinates

                    for (var i = 0; i < result.length; i++) {
                        addMarker(result[i]);
                    }
                    // Zoom to all markers
                    if (markerCoords.length > 0) {
                        var bounds = L.latLngBounds(markerCoords);
                        map.fitBounds(bounds, {
                            padding: [50, 50]
                        });
                    }
                }
            }
        ).done(function() {
            setTimeout(function() {
                $(".dismissButton").trigger("click");
            }, 200);
        });
    }
</script>