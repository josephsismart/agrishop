<script type="text/javascript">
var mapProduction        = null;
var markersLayerProduction = null;

// ── Init map only when modal is first opened ───────────────
$('#modalSearchProduction').one('shown.bs.modal', function() {
    if (!mapProduction) {
        mapProduction = L.map('productionMap').setView([8.9492, 125.5436], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapProduction);
        markersLayerProduction = L.layerGroup().addTo(mapProduction);
    }
    mapProduction.invalidateSize();
    searchProductionMap(); // load all on first open
});

// ── Resize map on subsequent opens ────────────────────────
$('#modalSearchProduction').on('shown.bs.modal', function() {
    if (mapProduction) {
        setTimeout(function() { mapProduction.invalidateSize(); }, 200);
    }
});

// ── Enter key search ───────────────────────────────────────
$(document).on('keydown', '#searchProductionProduce', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchProductionMap(); }
});

function searchProductionMap() {
    if (!mapProduction || !markersLayerProduction) return;

    var value = $('#searchProductionProduce').val();
    markersLayerProduction.clearLayers();

    $.ajax({
        url: "<?= base_url('userfarmer/OnProduction/searchProduction') ?>",
        type: "GET",
        dataType: "json",
        data: { value: value },
        success: function(response) {

            var bounds = [];
            var count  = 0;

            $.each(response, function(i, item) {
                if (!item.lat || !item.lon) return;
                count++;

                var statusColor = item.status === 'PLANTED'          ? '#28a745'
                    : item.status === 'GROWING'                       ? '#17a2b8'
                    : item.status === 'READY_TO_HARVEST'              ? '#e67e22'
                    : item.status === 'HARVESTED'                     ? '#6f42c1'
                    : '#6c757d';

                // ── Produce image pin icon ─────────────────
                var pinIcon = L.divIcon({
                    className: '',
                    html:
                        '<div style="position:relative;width:48px;">' +
                        // Produce image circle
                        '<div style="width:44px;height:44px;border-radius:50%;overflow:hidden;' +
                        'border:3px solid ' + statusColor + ';box-shadow:0 2px 6px rgba(0,0,0,.35);' +
                        'background:#fff;">' +
                        '<img src="' + item.produce_img_raw + '" width="44" height="44" ' +
                        'style="object-fit:cover;width:100%;height:100%;" ' +
                        'onerror="this.src=\'<?= base_url("dist/img/media/icons/1x1.png") ?>\'"/>' +
                        '</div>' +
                        // Status dot
                        '<div style="position:absolute;bottom:0;right:0;width:14px;height:14px;' +
                        'border-radius:50%;background:' + statusColor + ';border:2px solid #fff;"></div>' +
                        // Triangle pointer
                        '<div style="width:0;height:0;border-left:8px solid transparent;' +
                        'border-right:8px solid transparent;border-top:10px solid ' + statusColor + ';' +
                        'margin:0 auto;"></div>' +
                        '</div>',
                    iconSize: [48, 58],
                    iconAnchor: [24, 58],
                    popupAnchor: [0, -60]
                });

                var marker = L.marker([item.lat, item.lon], { icon: pinIcon });

                // ── Rich popup ─────────────────────────────
                var popup =
                    "<div style='min-width:300px;max-width:340px;font-size:13px;'>" +

                    // Farmer row
                    "<div style='display:flex;align-items:center;gap:8px;margin-bottom:8px;'>" +
                    "<img src='" + item.farmer_img_raw + "' width='42' height='42' " +
                    "style='border-radius:50%;object-fit:cover;border:2px solid #28a745;flex-shrink:0;' " +
                    "onerror=\"this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'\">" +
                    "<div>" +
                    "<div style='font-weight:700;color:#111;'><i class='fa fa-user mr-1 text-success'></i>" + item.farmer_name + "</div>" +
                    "<div style='font-size:11px;color:#666;'><i class='fa fa-home mr-1'></i>" + item.farm_name + "</div>" +
                    "<div style='font-size:11px;color:#888;'>" + item.farm_location + "</div>" +
                    "</div></div>" +

                    "<hr style='margin:6px 0;'>" +

                    // Produce row
                    "<div style='display:flex;align-items:center;gap:8px;margin-bottom:8px;'>" +
                    "<img src='" + item.produce_img_raw + "' width='56' height='56' " +
                    "style='border-radius:10px;object-fit:cover;border:2px solid #dee2e6;flex-shrink:0;' " +
                    "onerror=\"this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'\">" +
                    "<div>" +
                    "<div style='font-weight:700;font-size:15px;color:#111;'>" + item.produce + "</div>" +
                    (item.variety ? "<div style='font-size:11px;color:#666;font-style:italic;'>" + item.variety + "</div>" : "") +
                    "<span style='display:inline-block;margin-top:3px;background:" + statusColor + ";color:#fff;" +
                    "padding:2px 10px;border-radius:12px;font-size:10px;font-weight:700;'>" + item.status + "</span>" +
                    "</div></div>" +

                    "<hr style='margin:6px 0;'>" +

                    // Details grid
                    "<div style='display:grid;grid-template-columns:1fr 1fr;gap:5px;'>" +

                    "<div style='background:#f8f9fa;border-radius:7px;padding:6px 8px;'>" +
                    "<div style='font-size:9px;color:#888;text-transform:uppercase;font-weight:700;'>📅 Planted</div>" +
                    "<div style='font-weight:600;font-size:12px;'>" + (item.planted_date || '—') + "</div>" +
                    "</div>" +

                    "<div style='background:#f8f9fa;border-radius:7px;padding:6px 8px;'>" +
                    "<div style='font-size:9px;color:#888;text-transform:uppercase;font-weight:700;'>📐 Area</div>" +
                    "<div style='font-weight:600;font-size:12px;'>" + (item.area_sqm || '—') + "</div>" +
                    "</div>" +

                    "<div style='background:#d4edda;border-radius:7px;padding:6px 8px;'>" +
                    "<div style='font-size:9px;color:#155724;text-transform:uppercase;font-weight:700;'>🌾 Est. Harvest</div>" +
                    "<div style='font-weight:700;font-size:12px;color:#155724;'>" + (item.expected_harvest_date || '—') + "</div>" +
                    "</div>" +

                    "<div style='background:#d4edda;border-radius:7px;padding:6px 8px;'>" +
                    "<div style='font-size:9px;color:#155724;text-transform:uppercase;font-weight:700;'>⚖️ Est. Yield</div>" +
                    "<div style='font-weight:700;font-size:12px;color:#155724;'>" + (item.expected_yield || '—') + "</div>" +
                    "</div>" +

                    "<div style='background:#cce5ff;border-radius:7px;padding:6px 8px;'>" +
                    "<div style='font-size:9px;color:#004085;text-transform:uppercase;font-weight:700;'>💰 Est. Revenue</div>" +
                    "<div style='font-weight:700;font-size:12px;color:#004085;'>" + (item.expected_revenue || '—') + "</div>" +
                    "</div>" +

                    "<div style='background:#cce5ff;border-radius:7px;padding:6px 8px;'>" +
                    "<div style='font-size:9px;color:#004085;text-transform:uppercase;font-weight:700;'>🏷️ Market Price</div>" +
                    "<div style='font-weight:700;font-size:12px;color:#004085;'>₱" + (item.market_price_per_kg || '—') + "/kg</div>" +
                    "</div>" +

                    "</div>" + // end grid

                    (item.note ?
                        "<div style='margin-top:7px;padding:6px 8px;background:#fff3cd;border-radius:7px;font-size:11px;'>" +
                        "<i class='fa fa-sticky-note mr-1 text-warning'></i><i>" + item.note + "</i></div>" : "") +

                    "<div style='margin-top:6px;font-size:11px;color:#888;padding-top:5px;border-top:1px solid #f0f0f0;'>" +
                    "<i class='fa fa-phone mr-1'></i>" + item.farmerContact +
                    "</div>" +

                    "</div>"; // end popup

                marker.bindPopup(popup, { maxWidth: 350 });
                marker.addTo(markersLayerProduction);
                bounds.push([item.lat, item.lon]);
            });

            // Zoom to results
            if (bounds.length > 0) {
                mapProduction.fitBounds(bounds, { padding: [50, 50] });
            }

            // Result count
            var msg = count === 0
                ? '<i class="fa fa-info-circle mr-1"></i>No results found. Try a different keyword.'
                : '<i class="fa fa-map-pin mr-1 text-success"></i><b>' + count + '</b> production record(s) found on map.';
            $('#searchResultCount').html(msg);
        }
    });
}
</script>
