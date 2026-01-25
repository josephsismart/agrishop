<script type="text/javascript">
    let farm_id = null;
    let transaction_id_ = null;
    const farmCache = {};
    document.addEventListener("DOMContentLoaded", function() {
        const nav = document.getElementById("topNav");
        const navHeight = nav.offsetHeight;

        // Set CSS variable dynamically
        document.documentElement.style.setProperty('--nav-height', navHeight + 'px');

        // If the map already exists, refresh size
        setTimeout(() => {
            if (window.map) map.invalidateSize();
        }, 200);
    });
    window.onload = () => {
        $('.modal').on('shown.bs.modal', () => window.dispatchEvent(new Event('resize')));
        // $('#homeModal').modal('show');
        // $("#searchProduce").val('tomato');
        // triggerSearch();
    }


    $(document).on('click', '.barangay-item', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');

        // Set selected value to input
        $('.barangayInput').val(name);

        // Store barangay id in hidden input (recommended)
        $('#barangay_id').val(id);

        // Hide dropdown
        $('.barangayResults').hide();
        $('[name=barangay]').val(id)
    });

    $('.barangayInput').on('keyup', function() {
        let keyword = $(this).val();

        if (keyword.length < 3) {
            $('.barangayResults').hide();
            return;
        }

        $.ajax({
            url: "<?= base_url('search-barangay') ?>",
            type: "POST",
            data: {
                keyword: keyword,
                limit: 3
            },
            success: function(res) {
                let data = JSON.parse(res);

                if (data.length === 0) {
                    $('.barangayResults').hide();
                    return;
                }

                let html = "";
                data.forEach(row => {
                    html += `<li class="list-group-item barangay-item" data-id="${row.id}" data-name="${row.text}">
                    ${row.text}
                </li>`;
                });

                $('.barangayResults').html(html).show();
            }
        });
    });


    // when clicked
    $(document).on('click', '.barangay-item', function() {
        let name = $(this).data('name');
        let id = $(this).data('id');

        $('.barangayInput').val(name); // show selected barangay
        $('.barangayResults').hide(); // hide list

        // optional: save to hidden field if needed
        // $("#barangay_id").val(id);
    });

    var valid = 0;

    function validate(form_id) {
        let invalid = 0;
        $($("#" + form_id).find("input").get().reverse()).each(function() {
            if ($("#" + form_id + ' input[type="search"]')) {
                // return 0;
            }
            if ($("#" + form_id + ' input[type="text"]')) {
                var name = clean($(this).attr("name"));
                var nr = $(this).attr("nr");

                if (name == null) {} else if (nr != 1) {
                    if (!$(this).val()) {
                        $(this).focus().addClass("is-invalid");
                        $("#" + form_id + " ." + name).addClass('border-danger');
                        invalid++;
                    } else {
                        $(this).removeClass("is-invalid");
                        $("#" + form_id + " ." + name).removeClass('border-danger');
                    }
                }
            }
        });
        valid = invalid;
    }

    function passwordChecker(a, b, c) {
        let f = "form_save_data" + a;
        let g = $("#" + f + " ." + b).val(); // password
        let h = $("#" + f + " ." + c).val(); // confirm password

        // --- LENGTH CHECK (Password only) ---
        if (g.length >= 8) {
            $("#" + f + " .atleast").hide();
            $("#" + f + " .good8").show();
        } else {
            $("#" + f + " .atleast").show();
            $("#" + f + " .good8").hide();
        }

        // --- MATCH CHECK (always active) ---
        if (!g && !h) {
            // nothing typed yet
            $("#" + f + " .good").hide();
            $("#" + f + " .bad").hide();
        } else if (g === h) {
            // matches
            $("#" + f + " .good").show();
            $("#" + f + " .bad").hide();
        } else {
            // mismatch
            $("#" + f + " .good").hide();
            $("#" + f + " .bad").show();
        }

        // --- ENABLE SUBMIT ---
        if (g.length >= 8 && g === h) {
            $("#" + f + " .submitBtnPrimary").prop("disabled", false);
        } else {
            $("#" + f + " .submitBtnPrimary").prop("disabled", true);
        }
    }

    function saveForm(formId, tblId, tbl, dtd, pl) {
        let a = "";
        var saveData = {
            clearForm: false,
            resetForm: false,
            beforeSubmit: function(e) {
                validate("form_save_data" + formId);
                if (valid != 0) {
                    fillIn();
                    return false;
                }
                a = $("#form_save_data" + formId + " .submitBtnPrimary").text();
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", true);
                $("#form_save_data" + formId + " .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
            },
            success: function(data) {
                var d = JSON.parse(data);
                if (d.success == true) {
                    successAlert("Successfully Updated!");
                    // toastr.success("Successfully Applied!")
                    $(".sbmtbttn").hide();
                    $(".redirect").show();
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 2000)
                } else if (d.exist == true) {
                    existAlert("Application already exist!");
                    // toastr.warning("Application already exist!")
                } else {
                    failAlert("Something went wrong!");
                }
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                $("#form_save_data" + formId + " .submitBtnPrimary").html(a);
            }
        };
        $("#form_save_data" + formId).ajaxForm(saveData);
    }

    saveForm("RegisterFarmer", [null], null);
    saveForm("UpdateProfile", [null], null);

    function imageView(a, b, c) {
        var fileInput = $("[name=" + a + "]")[0]; // Get the file input element
        var file = fileInput.files[0]; // Get the selected file

        if (file.size > 25 * 1024 * 1024) {
            // Picture size is above 2MB
            alert("Picture must be less than 2MB");
            return; // You can handle this case according to your requirements
        }

        var reader = new FileReader();

        reader.onload = function(e) {
            $("[name=" + b + "]").attr('src', e.target.result); // Set the source of the image element
        };

        reader.readAsDataURL(file);
    }

    function defaultImg(a, b, c, d) {
        var reader = new FileReader();
        $("[name=picProduce]").val("");
        // img = (d == 'FEMALE' ? 'defaultf.png' : 'defaultm.png');
        $("[name=previewPicProduce]").attr("src", "<?= $system_svg_1x1 ?>");
        reader.onload = function(e) {
            document.getElementById(b).src = e.target.result;
        };
    }


    function clean(a) {
        var str = a;
        return str === undefined ? null : str.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
    }
    const Toast = Swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 3000
    });

    function successAlert(a) {
        Toast.fire({
            icon: 'success',
            title: '  ' + a
        })
    }

    function failAlert(a) {
        Toast.fire({
            icon: 'error',
            title: '  ' + a
        })
    }

    function infoAlert(a) {
        Toast.fire({
            icon: 'info',
            title: '  ' + a
        })
    }

    function fillIn() {
        Toast.fire({
            icon: 'error',
            title: '  Please fill in all the required fields.'
        })
    }

    function existAlert(a) {
        Toast.fire({
            icon: 'warning',
            title: '  ' + a
        })
    }

    function noData(a) {
        Toast.fire({
            icon: 'warning',
            title: '  ' + a,
        })
    }

    function getTable(tableId, dtd, pl) {
        var drawCounter = 0;
        $("#tbl" + tableId).DataTable().destroy();
        var table, table_data = $("#tbl" + tableId).DataTable({
            "order": [
                [0, "asc"]
            ],
            dom: 'Bfrtip',
            buttons: [],
            "info": pl == -1 ? false : true,
            "paging": pl == -1 ? false : true,
            "ordering": pl == -1 ? false : true,
            "oLanguage": {
                "sSearch": ""
            },
            "processing": true,
            "serverSide": true,
            language: {
                searchPlaceholder: "Search...",
            },
            ajax: {
                url: "<?= base_url('userpublicmap/map/get') ?>" + tableId,
                type: "POST",
                data: function(d) {
                    drawCounter++;
                    d.length = pl;
                    d.draw = drawCounter;
                    d.search.value = $('#tbl' + tableId + '_filter input').val();
                    d.search.farm_id = farm_id;
                    d.search.transaction_id = transaction_id_;
                }
            },

            "lengthMenu": [5, 10, 25, 50, 100],
            "pageLength": pl,
        });

        $("#tbl" + tableId).on('draw.dt', function() {
            $(".searchBtn").attr("disabled", false);
            $(".searchBtn").html("<span class=\"fa fa-search\"></span>");
            $(".collapse" + tableId).trigger('click');
        });
        $("#tbl" + tableId + "_filter").addClass("row");
        $("#tbl" + tableId + "_filter label").css("width", "97%");
        $("#tbl" + tableId + "_filter .form-control-sm").css("width", "97%");
        dtd == 1 ? $("#tbl" + tableId).DataTable().destroy() : "";
    }



    // Initialize map
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
            ${farmImgHtml}
            <button class="btn btn-primary btn-sm reserve-btn" data-id="${farm.id}"
                onclick='orderNow(${farm.id})'>
                Order Now
            </button>
            <br>
            <i class="badge bg-warning text-black" style="font-size: 14px;">${farm.farm_name}</i><br>
            <b style="font-size: 11px;">${farm.farm_location}</b><br>
            <i class="text-muted">Location: ${lat}, ${lon}</i><br>
            ${produceHtml}
            <br>
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
            if (value.length < 3) {
                existAlert("at least 3 characters!");
                return;
            }
            $.post("<?= base_url() ?>" + "userpublicmap/map/searchProduce", {
                    value: value
                },
                function(a) {
                    var result = JSON.parse(a);
                    if (result == "") {
                        infoAlert("No results found!");
                    } else {


                        // // When search returns data
                        // function showMapWithMarkers(data) {
                        //     // Show the map container
                        //     $("#landing_page_map").slideDown(400, function() {
                        //         // After slide, fix Leaflet map

                        //         // Optional: recenter / fit bounds
                        //         if (data && data.length > 0) {
                        //             const bounds = [];
                        //             data.forEach(farm => {
                        //                 const latLng = [parseFloat(farm.lat), parseFloat(farm.lon)];
                        //                 bounds.push(latLng);

                        //                 // Add marker
                        //                 L.marker(latLng).addTo(map)
                        //                     .bindPopup(farm.name);
                        //             });

                        //             map.fitBounds(bounds);
                        //         }
                        //     });
                        // }



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
    })
</script>