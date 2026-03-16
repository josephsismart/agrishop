<script type="text/javascript">
    var mapProduction;
    var markersLayerProduction;

    $(document).ready(function() {

        mapProduction = L.map('productionMap').setView([8.9492, 125.5436], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapProduction);

        markersLayerProduction = L.layerGroup().addTo(mapProduction);

        searchProductionMap(); // initial load

        $('#modalSearchProduction').on('shown.bs.modal', function() {

            setTimeout(function() {

                mapProduction.invalidateSize();
                mapProduction.setView([8.9492, 125.5436], 10);

            }, 200);

        });

    });

    function searchProductionMap() {

        var value = $("#searchProductionProduce").val();

        markersLayerProduction.clearLayers(); // remove old markers

        $.ajax({
            url: "<?= base_url('userfarmer/OnProduction/searchProduction') ?>",
            type: "GET",
            dataType: "json",
            data: {
                value: value
            },
            success: function(response) {

                var bounds = []; // store coordinates for zoom

                $.each(response, function(i, item) {

                    if (item.lat && item.lon) {

                        var marker = L.marker([item.lat, item.lon]).addTo(markersLayerProduction);

                        var popupContent =
                            "<div style='min-width:270px'>" +

                            "<div class='d-flex align-items-center mb-2'>" +
                            item.farmer_img_path +
                            "<div class='ml-2'>" +
                            "<b>" + item.farmer_name + "</b><br>" +
                            "<small>" + item.farm_name + "</small>" +
                            "</div>" +
                            "</div>" +

                            "<hr class='m-1'>" +

                            "<div class='d-flex'>" +

                            "<div class='mr-2'>" +
                            item.produce_img_path +
                            "</div>" +

                            "<div>" +
                            "<b>" + item.produce + "</b><br>" +
                            "<small>Area: <b>" + item.area_sqm + "</b></small><br>" +
                            "<small>Planted: <b>" + item.planted_date + "</b></small><br>" +
                            "<small>Status: <span class='badge badge-success'>" + item.status + "</span></small>" +
                            "</div>" +

                            "</div>" +

                            "<hr class='m-1'>" +

                            "<small><b>Location:</b> " + item.farm_location + "</small><br>" +
                            "<small>" + item.farmerContact + "</small>" +

                            "</div>";

                        marker.bindPopup(popupContent);

                        bounds.push([item.lat, item.lon]); // store location
                    }

                });

                // Zoom to searched markers
                if (bounds.length > 0) {
                    mapProduction.fitBounds(bounds, {
                        padding: [50, 50]
                    });
                }

            }
        });

    }
</script>