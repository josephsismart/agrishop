<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
$uri_reports = "reports";
$sy_ = $getOnLoad["sy"]; //$getOnLoad["sy_qrtr_e_g"];
?>
<!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
<!-- <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script> -->
<script src="<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/code/highmaps.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/code/highcharts.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/code/modules/map.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/code/modules/tiledwebmap.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/code/modules/data.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/code/modules/exporting.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/code/modules/offline-exporting.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/code/modules/accessibility.js"></script>
<script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>

<!-- <script src="<?= base_url() ?>plugins/Highcharts-11.1.0/modules/exporting.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-11.1.0/modules/export-data.js"></script>
<script src="<?= base_url() ?>plugins/Highcharts-11.1.0/modules/accessibility.js"></script> -->



<script type="text/javascript">
    getCityMun();

    function getCityMun() {
        (async () => {
            const topology = await fetch(
                // '<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/map/abc_butuan.topo.json'
                // '<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/map/abc_adn_brgy.geojson'
                // abc_adn_brgy.geojson
                '<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/map/adn.topojson'
            ).then(response => response.json());

            $.get("<?= base_url($uri . '/dashboard/getMapPlotCityMun') ?>",
                function(data) {
                    var d = JSON.parse(data);

                    function pointClick(event) {
                        var point = event.point;
                        var pointD = point.properties.gid; // Access the ID of the clicked point
                        var pointB = point.properties.adm_id; // Access the ID of the clicked point
                        var pointN = point.properties.name; // Access the ID of the clicked point
                        // Now, you can use the pointID as needed
                        // Additional actions you want to perform on point click
                        $(".container1").slideToggle();
                        getCityMunBrgy(pointD,pointB,pointN);
                    }

                    Highcharts.mapChart('container1', {
                        chart: {
                            map: topology,
                            // styledMode: true,
                            zoom: 10 // set the initial zoom level
                        },

                        title: {
                            text: 'Learners based on Map',
                            align: 'left'
                        },

                        subtitle: {
                            text: 'Number of learners according to their barangay',
                            align: 'left'
                        },

                        mapNavigation: {
                            enabled: true,
                            buttonOptions: {
                                // alignTo: 'spacingBox',
                                y: 20,
                                x: -70
                            }
                        },

                        accessibility: {
                            series: {
                                descriptionFormat: '{series.name}, map with {series.points.gid} areas.',
                                pointDescriptionEnabledThreshold: 50

                            }
                        },

                        colorAxis: {
                            // min: 0,
                            // stops: [
                            //     [0, '#f0fcfa'],
                            //     [0.5, Highcharts.getOptions().colors[0]],
                            //     [
                            //         1,
                            //         Highcharts.color(Highcharts.getOptions().colors[0])
                            //         .brighten(-0.5).get()
                            //     ]
                            // ],

                            // dataClasses: [{
                            //     from: 1451,
                            //     to: 1451,
                            //     color: '#0200D0',
                            //     name: this.properties.gid
                            // }, {
                            //     from: 0,
                            //     to: 100,
                            //     color: '#C40401',
                            //     name: 'Trump'
                            // }]
                        },

                        legend: {
                            enabled: true,
                            layout: 'vertical',
                            align: 'left',
                            // verticalAlign: 'top'
                            verticalAlign: 'middle',
                            formatter: function() {
                                // Map labels to colors here
                                if (this.value === 1451) {
                                    return '<span style="color: red;">Custom Color</span>';
                                } else {
                                    return this.value;
                                }
                            }
                        },

                        tooltip: {
                            headerFormat: '',
                            formatter: function() {
                                var point = this.point; // Get the current point
                                var citymuncode = point.properties.citymuncode; // Extract citymuncode from the point's properties
                                var maleValue = d['data_sex_m'][citymuncode] || 0; // Use 0 as a default value if maleValue is undefined
                                var fmaleValue = d['data_sex_f'][citymuncode] || 0; // Use 0 as a default value if maleValue is undefined

                                // Create the tooltip content
                                var tooltip = '<div style="width: 300rem; height: 200rem;">'; // Set the width and height here
                                tooltip += '<b>' + point.name.toUpperCase() + '</b><br>' +
                                    'Male: <b style="color:#1976D2">' + maleValue.toLocaleString() + '</b><br>' +
                                    'Female: <b style="color:#E91E63">' + fmaleValue.toLocaleString() + '</b><br>' +
                                    'Total: <b>' + point.value.toLocaleString() + '</b>';
                                tooltip += '</div>'; // Close the div

                                // const row = this.options.row,
                                // $div = $('<div></div>')
                                //     .dialog({
                                //         title: "container" + brgycode,
                                //         width: 320,
                                //         height: 300
                                //     });

                                // viewChart(brgycode);

                                // Add a button to trigger the viewMore function with male and female values

                                return tooltip;
                            }
                        },

                        series: [{
                            name: 'Country',
                            data: d['data_map'],
                            accessibility: {
                                exposeAsGroupOnly: true
                            },
                            // borderColor: '#606060',
                            // nullColor:colors[2],
                            color: 'red',
                            showInLegend: false,
                            dataLabels: {
                                enabled: true,
                                color: '#FFFFFF',
                                format: '{point.properties.name}',
                                // nullFormat: ''
                            },
                            point: {
                                events: {
                                    click: pointClick
                                }
                            },

                        }]
                    });
                }).done(function() {
                $(".container1").slideToggle();
            });
        })();
    }

    function getCityMunBrgy(a,b,c) {
        let fileName = a.toString()+'.geojson';
        (async () => {
            const topology = await fetch(
                '<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/map/'+fileName
            ).then(response => response.json());

            $.get("<?= base_url($uri . '/dashboard/getMapPlot') ?>",{cc:b},
                function(data) {
                    var d = JSON.parse(data);

                    function pointClick() {
                        // Get the brgycode
                        var brgycode = this.properties.brgycode;

                        // Create a unique ID for the modal container based on brgycode
                        var modalId = 'container' + brgycode;


                        // Show the modal
                        $('#modalMapSexGraph #label').text(this.name + ' - ' + this.value);
                        $('#modalMapSexGraph').modal('show');
                        var mmm = parseInt(d['data_sex_m'][brgycode], 10);
                        var fff = parseInt(d['data_sex_f'][brgycode], 10);

                        // Create the pie chart inside the modal
                        window.chart = new Highcharts.Chart({
                            chart: {
                                renderTo: 'containerGraph', // Render to the chart-container within the modal
                                type: 'pie',
                                width: 290,
                                height: 240
                            },
                            title: {
                                text: null
                            },
                            legend: {
                                enabled: true,
                                reversed: true
                            },
                            series: [{
                                name: 'Learners',
                                data: [{
                                    name: 'MALE: <b>' + mmm + '</b>',
                                    color: '#C40401',
                                    y: mmm
                                }, {
                                    name: 'FEMALE: <b>' + fff + '</b>',
                                    color: '#0200D0',
                                    y: fff
                                }],
                                dataLabels: {
                                    format: '{point.percentage:.1f}%'
                                },
                                showInLegend: true
                            }]
                        });

                        // Close the modal when clicking the close button (x)
                        $('#' + modalId + ' .close').click(function() {
                            $('#' + modalId).hide();
                        });

                        // Close the modal when clicking outside the modal content
                        $(window).click(function(event) {
                            if (event.target.id === modalId) {
                                $('#' + modalId).hide();
                            }
                        });
                    }

                    Highcharts.mapChart('container1', {
                        chart: {
                            map: topology,
                            // styledMode: true,
                            zoom: 10 // set the initial zoom level
                        },

                        mapNavigation: {
                            enabled: true,
                            buttonOptions: {
                                alignTo: 'spacingBox'
                            }
                        },

                        // mapView: {
                        //     center: [10.73028454146517, 59.91261204279989],
                        // },

                        title: {
                            text: ''+ c + " <a href='#' style='color:red;' id='backButton'>back</a>",
                            align: 'left'
                        },

                        subtitle: {
                            text: 'Number of learners according to their barangay',
                            align: 'left'
                        },

                        mapNavigation: {
                            enabled: true,
                            buttonOptions: {
                                // alignTo: 'spacingBox',
                                y: 20,
                                x: -70
                            }
                        },

                        accessibility: {
                            series: {
                                descriptionFormat: '{series.name}, map with {series.points.gid} areas.',
                                pointDescriptionEnabledThreshold: 50

                            }
                        },

                        colorAxis: {
                            min: 0,
                            stops: [
                                [0, '#f0fcfa'],
                                [0.5, Highcharts.getOptions().colors[0]],
                                [
                                    1,
                                    Highcharts.color(Highcharts.getOptions().colors[0])
                                    .brighten(-0.5).get()
                                ]
                            ]
                        },

                        legend: {
                            enabled: true,
                            layout: 'vertical',
                            align: 'left',
                            // verticalAlign: 'top'
                            verticalAlign: 'middle'
                        },

                        tooltip: {
                            headerFormat: '',
                            formatter: function() {
                                var point = this.point; // Get the current point
                                var brgycode = point.properties.brgycode; // Extract brgycode from the point's properties
                                var maleValue = d['data_sex_m'][brgycode] || 0; // Use 0 as a default value if maleValue is undefined
                                var fmaleValue = d['data_sex_f'][brgycode] || 0; // Use 0 as a default value if maleValue is undefined

                                // Create the tooltip content
                                var tooltip = '<div style="width: 300rem; height: 200rem;">'; // Set the width and height here
                                tooltip += '<b>' + point.name.toUpperCase() + '</b><br>' +
                                    'Male: <b style="color:#1976D2">' + maleValue.toLocaleString() + '</b><br>' +
                                    'Female: <b style="color:#E91E63">' + fmaleValue.toLocaleString() + '</b><br>' +
                                    'Total: <b>' + point.value.toLocaleString() + '</b>';
                                tooltip += '</div>';

                                // const row = this.options.row,
                                // $div = $('<div></div>')
                                //     .dialog({
                                //         title: "container" + brgycode,
                                //         width: 320,
                                //         height: 300
                                //     });

                                // viewChart(brgycode);

                                // Add a button to trigger the viewMore function with male and female values

                                return tooltip;
                            }
                        },

                        series: [{
                            name: 'Country',
                            data: d['data_map'],provider: {
                                type: "OpenStreetMap"
                            },
                            // type: "tiledwebmap",
                            accessibility: {
                                exposeAsGroupOnly: true
                            },
                            // borderColor: '#606060',
                            nullColor: 'rgba(200, 200, 200, 0.3)',
                            showInLegend: false,
                            // dataLabels: {
                            //     enabled: true,
                            //     color: '#FFFFFF',
                            //     format: '{point.name}',
                            //     nullFormat: ''
                            // },
                            point: {
                                events: {
                                    click: pointClick
                                }
                            },

                        }]
                    });
                    // Add an event listener to the "BACK" link
                    document.getElementById('backButton').addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior (navigation)
                        $(".container1").slideToggle();
                        getCityMun(); // Replace with your desired action
                    });
                }).done(function() {
                $(".container1").slideToggle();
            });
        })();
    }


    (async () => {
        // const topology = await fetch(
        //     // '<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/map/abc_butuan.topo.json'
        //     '<?= base_url() ?>plugins/Highcharts-Maps-11.1.0/map/abc_adn.geojson'
        // ).then(response => response.json());

        $.get("<?= base_url($uri . '/dashboard/getPopulationLearner') ?>",
            function(data) {
                var d = JSON.parse(data);

                // Custom template helper
                Highcharts.Templating.helpers.abs = value => Math.abs(value);

                // Age categories
                const categories = d['data_age'];

                Highcharts.chart('container2', {
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: 'Population for Learners',
                        align: 'left'
                    },
                    subtitle: {
                        text: 'Learners data based on sex and age.',
                        align: 'left'
                    },
                    accessibility: {
                        point: {
                            valueDescriptionFormat: '{index}. Age {xDescription}, {value}'
                        }
                    },
                    xAxis: [{
                        categories: categories,
                        reversed: false,
                        labels: {
                            step: 1
                        },
                        accessibility: {
                            description: 'Age (male)'
                        }
                    }, { // mirror axis on right side
                        opposite: true,
                        reversed: false,
                        categories: categories,
                        linkedTo: 0,
                        labels: {
                            step: 1
                        },
                        accessibility: {
                            description: 'Age (female)'
                        }
                    }],
                    yAxis: {
                        title: {
                            text: null
                        },
                        labels: {
                            format: '{abs value}'
                        },
                        // accessibility: {
                        //     description: 'Percentage population',
                        //     rangeDescription: 'Range: 0 to 5%'
                        // }
                    },

                    plotOptions: {
                        series: {
                            stacking: 'normal',
                            // borderRadius: '50%'
                        }
                    },

                    tooltip: {
                        format: '<b>{series.name}, age {point.category}</b><br/>' +
                            'Population: {(abs point.y)}'
                    },
                    series: [{
                        name: 'Male',
                        data: d['data_sex_m'],
                        color: '#1976D2'
                    }, {
                        name: 'Female',
                        data: d['data_sex_f'],
                        color: '#E91E63'
                    }]
                });
            }).done(function() {
            $(".container2").slideToggle();
        });
    })();
</script>