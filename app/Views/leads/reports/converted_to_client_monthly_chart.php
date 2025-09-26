<?php
$owner_name = isset($owner_name) ? $owner_name : "";
$default_interval = isset($clients_time_series_default_interval) ? $clients_time_series_default_interval : "daily";
$time_series_json = isset($clients_time_series) ? $clients_time_series : '{}';
?>
<div class="leads-monthly-charts">
    <div class="leads-day-wise-chart card-body">
        <h4 class="mb15"><?php echo !empty($owner_name) ? $owner_name . ' - ' : ''; ?><?php echo app_lang("clients"); ?></h4>
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
            <div class="btn-group" role="group">
                <?php
                $interval_buttons = array(
                    "daily" => app_lang("daily"),
                    "weekly" => app_lang("weekly"),
                    "monthly" => app_lang("monthly")
                );
                foreach ($interval_buttons as $interval => $label) {
                    $active_class = $default_interval === $interval ? " active" : "";
                    echo '<button type="button" class="btn btn-default converted-client-interval-btn' . $active_class . '" data-interval="' . $interval . '">' . $label . '</button>';
                }
                ?>
            </div>
            <div>
                <button id="download-leads-pdf" class="btn btn-default">
                    <i data-feather="download" class="icon-16"></i> <?php echo app_lang('download_pdf'); ?>
                </button>
            </div>
        </div>
        <canvas id="leads-day-wise-chart" style="width: 100%; height: 350px;"></canvas>
    </div>

    <div class="leads-volume-chart card-body mt40">
        <h4 class="mb15"><?php echo !empty($owner_name) ? $owner_name . ' - ' : ''; ?><?php echo app_lang("volume"); ?></h4>
        <canvas id="leads-volume-chart" style="width: 100%; height: 350px;"></canvas>
    </div>

    <div class="leads-potential-margin-chart card-body mt40">
        <h4 class="mb15"><?php echo !empty($owner_name) ? $owner_name . ' - ' : ''; ?><?php echo app_lang("potential_margin"); ?></h4>
        <canvas id="leads-potential-margin-chart" style="width: 100%; height: 350px;"></canvas>
    </div>

    <div class="card-body source-and-owner-wise-chart mt50 b-t pt40">
        <div class="row mb30">
            <div class="col-md-6 b-r">
                <div class="mt20"><strong><?php echo !empty($owner_name) ? $owner_name . ' - ' : ''; ?><?php echo app_lang("lead_source"); ?></strong></div>
                <div class="mt20 pt10">
                    <canvas id="leads-source-wise-chart" style="width:100%; height: 300px;"></canvas>
                </div>

            </div>
            <div class="col-md-6">
                <div class="mt20"><strong><?php echo !empty($owner_name) ? $owner_name . ' - ' : ''; ?><?php echo app_lang("owner"); ?></strong></div>
                <div class="mt20 pt10">
                    <canvas id="leads-owner-wise-chart" style="width:100%; height: 300px;"></canvas>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mt20"><strong><?php echo !empty($owner_name) ? $owner_name . ' - ' : ''; ?><?php echo app_lang("client_status"); ?></strong></div>
                <div class="mt20 pt10">
                    <canvas id="clients-status-wise-chart" style="width:100%; height: 300px;"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mt20"><strong><?php echo !empty($owner_name) ? $owner_name . ' - ' : ''; ?><?php echo app_lang("close_rate"); ?></strong></div>
                <div class="mt20 pt10">
                    <canvas id="clients-close-rate-chart" style="width:100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mt20"><strong><?php echo !empty($owner_name) ? $owner_name . ' - ' : ''; ?><?php echo app_lang("opportunity_status"); ?></strong></div>
                <div class="mt20 pt10">
                    <canvas id="lead-status-bar-chart" style="width:100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php echo view("clients/reports/volume_by_status_chart", array("labels" => $volume_by_status_labels, "volume_data" => $volume_by_status_data, "status_colors" => $volume_by_status_colors)); ?>
    <?php echo view("clients/reports/volume_by_source_chart", array("labels" => $volume_by_source_labels, "volume_data" => $volume_by_source_data)); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>

<script type="text/javascript">
    $(document).ready(function () {
        if (window.ChartDataLabels && Chart && Chart.plugins) {
            Chart.plugins.register(ChartDataLabels);
        }

        var timeSeriesBundle = <?php echo $time_series_json; ?> || {};
        var defaultInterval = "<?php echo $default_interval; ?>";
        var chartDefinitions = [
            {
                key: 'clients',
                elementId: 'leads-day-wise-chart',
                label: '<?php echo app_lang("clients"); ?>',
                borderColor: '#2196f3',
                backgroundColor: 'rgba(54, 162, 235, 0.2)'
            },
            {
                key: 'volume',
                elementId: 'leads-volume-chart',
                label: '<?php echo app_lang("volume"); ?>',
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.2)'
            },
            {
                key: 'potential_margin',
                elementId: 'leads-potential-margin-chart',
                label: '<?php echo app_lang("potential_margin"); ?>',
                borderColor: '#ff9800',
                backgroundColor: 'rgba(255, 152, 0, 0.2)'
            }
        ];

        var charts = {};
        var seriesDefaults = {labels: [], data: []};

        var sharedChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            tooltips: {
                callbacks: {
                    title: function (tooltipItem, data) {
                        return data['labels'][tooltipItem[0]['index']];
                    }
                }
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    fontColor: "#898fa9"
                }
            },
            scales: {
                xAxes: [
                    {
                        ticks: {
                            autoSkip: true,
                            fontColor: "#898fa9"
                        },
                        gridLines: {
                            color: 'rgba(107, 115, 148, 0.1)'
                        }
                    }
                ],
                yAxes: [{
                        gridLines: {
                            color: 'rgba(107, 115, 148, 0.1)'
                        },
                        ticks: {
                            fontColor: "#898fa9"
                        }
                    }]
            }
        };

        chartDefinitions.forEach(function (definition) {
            var ctx = document.getElementById(definition.elementId);
            if (!ctx) {
                return;
            }

            var initialSeries = (timeSeriesBundle[definition.key] && timeSeriesBundle[definition.key][defaultInterval]) || seriesDefaults;
            var initialLabels = Array.isArray(initialSeries.labels) ? initialSeries.labels.slice() : [];
            var initialData = Array.isArray(initialSeries.data) ? initialSeries.data.slice() : [];

            charts[definition.key] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: initialLabels,
                    datasets: [{
                            label: definition.label,
                            data: initialData,
                            fill: true,
                            borderColor: definition.borderColor,
                            backgroundColor: definition.backgroundColor,
                            borderWidth: 2
                        }]
                },
                options: $.extend(true, {}, sharedChartOptions)
            });
        });

        var $intervalButtons = $(".converted-client-interval-btn");
        var updateTimeSeriesCharts = function (interval) {
            chartDefinitions.forEach(function (definition) {
                var chart = charts[definition.key];
                if (!chart) {
                    return;
                }

                var intervalSeries = (timeSeriesBundle[definition.key] && timeSeriesBundle[definition.key][interval]) || seriesDefaults;
                chart.data.labels = Array.isArray(intervalSeries.labels) ? intervalSeries.labels.slice() : [];
                chart.data.datasets[0].data = Array.isArray(intervalSeries.data) ? intervalSeries.data.slice() : [];
                chart.update();
            });
        };

        $intervalButtons.on("click", function () {
            var $button = $(this),
                interval = $button.data("interval");

            if (!interval || $button.hasClass("active")) {
                return;
            }

            $intervalButtons.removeClass("active");
            $button.addClass("active");
            updateTimeSeriesCharts(interval);
        });

        $intervalButtons.filter('[data-interval="' + defaultInterval + '"]').addClass('active');
        updateTimeSeriesCharts(defaultInterval);





        var colorPlate = [
            '#14BAA0', '#FF3D67', '#3B81F6',  '#6165F2', '#F59F0F', '#FBCD16', '#E84C3D','#40E0D0', '#E67F22', 
            '#36A2EB', '#FF6283', '#4BC0C0', '#FF9F40', '#32CD32', '#9370DB', '#FFD700', '#008080', '#FF6347', '#7B68EE',
            '#40E0D0', '#FF4500', '#6A5ACD', '#00FF7F', '#8B008B', '#FF8C00', '#00CED1', '#FF69B4', '#48D1CC', '#FF1493',
            '#1E90FF', '#ADFF2F', '#8A2BE2', '#00FF00', '#9932CC', '#228B22', '#BA55D3', '#3CB371', '#800000', '#7FFFD4',
            '#8B0000', '#00FFFF', '#DC143C', '#00FF8C', '#FF0000', '#7FFF00', '#B22222', '#00FA9A', '#FF7F50', '#ADFF2F',
            '#8B4513', '#20B2AA', '#CD5C5C', '#98FB98', '#800080', '#66CDAA', '#FA8072', '#9ACD32', '#FF4500', '#8FBC8B'
        ];
        var owner_wise_data = <?php echo $owner_wise_data; ?>;

        var color = owner_wise_data.length <= 50 ? colorPlate.slice(0, owner_wise_data.length) : generateColors(owner_wise_data.length);


        new Chart(document.getElementById("leads-owner-wise-chart"), {
            type: 'pie',
            data: {
                labels: <?php echo $owner_wise_labels; ?>,
                datasets: [
                    {
                        data: owner_wise_data,
                        backgroundColor: color,
                        borderWidth: 0
                    }]
            },
            options: {
                cutoutPercentage: 0,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#333',
                        formatter: function(value) {
                            return parseInt(value, 10).toLocaleString();
                        }
                    }
                },
                tooltips: {
                    callbacks: {

                    }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        fontColor: "#898fa9"
                    }
                },
                animation: {
                    animateScale: true
                }
            }
        });

        // generate random color
        function generateColors(count) {
            var colors = [];

            for (var i = 0; i < count; i++) {
                var color = '#' + Math.floor(Math.random() * 12345678).toString(16);
                colors.push(color);
            }
            return colors;
        }




        new Chart(document.getElementById("leads-source-wise-chart"), {
            type: 'bar',
            data: {
                labels: <?php echo $source_wise_labels; ?>,
                datasets: [{
                        label: "<?php echo app_lang('lead_source'); ?>",
                        data: <?php echo $source_wise_data; ?>,
                        fill: true,
                        categoryPercentage: 0.3,
                        borderColor: '#00B493',
                        backgroundColor: '#00B493',
                        borderWidth: 2
                    }]},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        title: function (tooltipItem, data) {
                            return data['labels'][tooltipItem[0]['index']];
                        },
                        label: function (tooltipItem, data) {
                            return "<?php echo app_lang('clients'); ?>:" + data['datasets'][0]['data'][tooltipItem['index']];
                        }
                    }
                },
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                display: true
                            }
                        }],
                    yAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                display: true
                            }
                        }]
                }
            }
        });

        new Chart(document.getElementById("clients-status-wise-chart"), {
            type: 'pie',
            data: {
                labels: <?php echo $client_status_labels; ?>,
                datasets: [
                    {
                        data: <?php echo $client_status_data; ?>,
                        backgroundColor: <?php echo $client_status_colors; ?>,
                        borderWidth: 0
                    }]
            },
            options: {
                cutoutPercentage: 0,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#333',
                        formatter: function(value) {
                            return parseInt(value, 10).toLocaleString();
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        fontColor: "#898fa9"
                    }
                },
                animation: {
                    animateScale: true
                }
            }
        });

        new Chart(document.getElementById("clients-close-rate-chart"), {
            type: 'pie',
            data: {
                labels: <?php echo $close_rate_labels; ?>,
                datasets: [
                    {
                        data: <?php echo $close_rate_data; ?>,
                        backgroundColor: <?php echo $close_rate_colors; ?>,
                        borderWidth: 0
                    }]
            },
            options: {
                cutoutPercentage: 0,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#333',
                        formatter: function(value) {
                            return parseInt(value, 10).toLocaleString();
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        fontColor: "#898fa9"
                    }
                },
                animation: {
                    animateScale: true
                }
            }
        });

        new Chart(document.getElementById("lead-status-bar-chart"), {
            type: 'bar',
            data: {
                labels: <?php echo $client_status_labels; ?>,
                datasets: [{
                        label: "<?php echo app_lang('lead_status'); ?>",
                        data: <?php echo $client_status_data; ?>,
                        backgroundColor: <?php echo $client_status_colors; ?>,
                        borderColor: <?php echo $client_status_colors; ?>,
                        borderWidth: 1
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {display: false},
                plugins: {
                    datalabels: {
                        color: '#333',
                        formatter: function(value) {
                            return Number(value).toLocaleString();
                        }
                    }
                },
                scales: {
                    xAxes: [{
                            ticks: {autoSkip: false}
                        }],
                    yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function (value) {
                                    return Number(value).toLocaleString();
                                }
                            }
                        }]
                }
            }
        });



    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#download-leads-pdf").on("click", function () {
            var button = this;
            button.style.display = 'none';
            html2canvas(document.querySelector('.leads-monthly-charts')).then(function (canvas) {
                var imgData = canvas.toDataURL('image/png');
                var pdf = new jspdf.jsPDF();
                var width = pdf.internal.pageSize.getWidth();
                var height = canvas.height * width / canvas.width;
                pdf.addImage(imgData, 'PNG', 0, 0, width, height);
                pdf.save('leads-report.pdf');
                button.style.display = '';
            });
        });
    });
</script>

