<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div id="rep-conversion-rates-chart-card" class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><?php echo app_lang('conversion_rate'); ?> (<?php echo app_lang('owner'); ?>)</h4>
            </div>
            <div class="chart-container" style="height: 350px;">
                <canvas id="rep-conversion-rates-chart"></canvas>
            </div>
        </div>
    </div>

    <div class="card clearfix">
        <div class="table-responsive">
            <table id="rep-conversion-rates-table" class="display" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var chartLabels = <?php echo $chart_labels; ?>;
        var chartRates = <?php echo $chart_rates; ?>;
        var chartConversions = <?php echo $chart_conversions; ?>;
        var chartTotalLeads = <?php echo $chart_total_leads; ?>;

        if (!Array.isArray(chartLabels)) {
            chartLabels = [];
        }
        if (!Array.isArray(chartRates)) {
            chartRates = [];
        }
        if (!Array.isArray(chartConversions)) {
            chartConversions = [];
        }
        if (!Array.isArray(chartTotalLeads)) {
            chartTotalLeads = [];
        }

        var ctx = document.getElementById("rep-conversion-rates-chart").getContext("2d");
        var repConversionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: "<?php echo app_lang('conversion_rate'); ?>",
                    data: chartRates,
                    backgroundColor: '#3B81F6',
                    borderColor: '#3B81F6',
                    borderWidth: 1,
                    conversions: chartConversions,
                    totalLeads: chartTotalLeads
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {display: false},
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var rate = tooltipItem.yLabel || 0;
                            var lines = ["<?php echo app_lang('conversion_rate'); ?>: " + Number(rate).toLocaleString(undefined, {maximumFractionDigits: 2}) + "%"];

                            if (dataset.conversions) {
                                var conversions = dataset.conversions[tooltipItem.index] || 0;
                                lines.push("<?php echo app_lang('converted_to_client'); ?>: " + Number(conversions).toLocaleString());
                            }

                            if (dataset.totalLeads) {
                                var totalLeads = dataset.totalLeads[tooltipItem.index] || 0;
                                lines.push("<?php echo app_lang('total_leads'); ?>: " + Number(totalLeads).toLocaleString());
                            }

                            return lines;
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: {color: 'rgba(127,127,127,0.1)'},
                        ticks: {fontColor: '#898fa9'}
                    }],
                    yAxes: [{
                        gridLines: {color: 'rgba(127,127,127,0.1)'},
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#898fa9',
                            callback: function (value) {
                                return Number(value).toLocaleString(undefined, {maximumFractionDigits: 1}) + '%';
                            }
                        }
                    }]
                }
            }
        });

        var updateRepConversionChart = function (chartData) {
            if (!chartData) {
                chartData = {labels: [], rates: [], conversions: [], total_leads: []};
            }

            var labels = Array.isArray(chartData.labels) ? chartData.labels : [];
            var rates = Array.isArray(chartData.rates) ? chartData.rates : [];
            var conversions = Array.isArray(chartData.conversions) ? chartData.conversions : [];
            var totalLeads = Array.isArray(chartData.total_leads) ? chartData.total_leads : [];

            repConversionChart.data.labels = labels;
            repConversionChart.data.datasets[0].data = rates;
            repConversionChart.data.datasets[0].conversions = conversions;
            repConversionChart.data.datasets[0].totalLeads = totalLeads;
            repConversionChart.update();
        };

        $("#rep-conversion-rates-table").appTable({
            source: '<?php echo_uri("lead_conversion_reports/rep_conversion_rates"); ?>',
            filterDropdown: [
                {name: "owner_id", class: "w200", options: <?php echo $owners_dropdown; ?>},
                {name: "region_id", class: "w200", options: <?php echo $regions_dropdown; ?>},
                {name: "source_value", class: "w200", options: <?php echo $sources_dropdown; ?>},
                {name: "lead_status_id", class: "w200", options: <?php echo $statuses_dropdown; ?>}
            ],
            rangeDatepicker: [
                {startDate: {name: "created_start_date", value: ""}, endDate: {name: "created_end_date", value: ""}, label: "<?php echo app_lang('created_date'); ?>", showClearButton: true},
                {startDate: {name: "migration_start_date", value: ""}, endDate: {name: "migration_end_date", value: ""}, label: "<?php echo app_lang('conversion_date'); ?>", showClearButton: true}
            ],
            columns: [
                {title: "<?php echo app_lang('owner'); ?>", class: "all"},
                {title: "<?php echo app_lang('total_leads'); ?>", class: "text-center"},
                {title: "<?php echo app_lang('converted_to_client'); ?>", class: "text-center"},
                {title: "<?php echo app_lang('conversion_rate'); ?>", class: "text-right"},
                {title: "<?php echo app_lang('average_conversion_time'); ?>", class: "text-right"}
            ],
            order: [[2, "desc"]],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });

        $("#rep-conversion-rates-table").on("xhr.dt", function (e, settings, json) {
            if (json && json.chart) {
                updateRepConversionChart(json.chart);
            }
        });
    });
</script>
