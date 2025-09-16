<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div id="client-conversion-timeline-chart-container" class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><?php echo app_lang('converted_to_client'); ?></h4>
                <button id="download-client-conversion-timeline" class="btn btn-default">
                    <i data-feather="download" class="icon-16"></i> <?php echo app_lang('download_pdf'); ?>
                </button>
            </div>
            <div class="chart-container" style="height: 350px;">
                <canvas id="client-conversion-timeline-chart"></canvas>
            </div>
        </div>
    </div>

    <div class="card clearfix">
        <div class="table-responsive">
            <table id="client-conversion-timeline-table" class="display" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var timelineLabels = <?php echo $timeline_labels; ?>;
        var timelineValues = <?php echo $timeline_values; ?>;
        var timelineCumulative = <?php echo $timeline_cumulative; ?>;

        if (!Array.isArray(timelineLabels)) {
            timelineLabels = [];
        }
        if (!Array.isArray(timelineValues)) {
            timelineValues = [];
        }
        if (!Array.isArray(timelineCumulative)) {
            timelineCumulative = [];
        }

        var ctx = document.getElementById("client-conversion-timeline-chart").getContext("2d");
        var timelineChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: timelineLabels,
                datasets: [{
                    type: 'bar',
                    label: '<?php echo app_lang('converted_to_client'); ?>',
                    data: timelineValues,
                    backgroundColor: '#3B81F6',
                    borderColor: '#3B81F6',
                    borderWidth: 1
                }, {
                    type: 'line',
                    label: '<?php echo app_lang('total'); ?>',
                    data: timelineCumulative,
                    borderColor: '#8C54FF',
                    backgroundColor: 'rgba(140, 84, 255, 0.15)',
                    borderWidth: 2,
                    fill: false,
                    yAxisID: 'y-axis-2',
                    pointRadius: 3,
                    pointBackgroundColor: '#8C54FF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {position: 'bottom'},
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                            var value = tooltipItem.yLabel;
                            if (datasetLabel) {
                                return datasetLabel + ': ' + Number(value).toLocaleString();
                            }
                            return Number(value).toLocaleString();
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: {color: 'rgba(127,127,127,0.1)'},
                        ticks: {fontColor: '#898fa9'}
                    }],
                    yAxes: [{
                        id: 'y-axis-1',
                        position: 'left',
                        gridLines: {color: 'rgba(127,127,127,0.1)'},
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#898fa9',
                            callback: function (value) {
                                return Number(value).toLocaleString();
                            }
                        }
                    }, {
                        id: 'y-axis-2',
                        position: 'right',
                        gridLines: {display: false},
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#898fa9',
                            callback: function (value) {
                                return Number(value).toLocaleString();
                            }
                        }
                    }]
                }
            }
        });

        var updateTimelineChart = function (timeline) {
            if (!timeline) {
                timeline = {labels: [], values: [], cumulative: []};
            }

            var labels = Array.isArray(timeline.labels) ? timeline.labels : [];
            var values = Array.isArray(timeline.values) ? timeline.values : [];
            var cumulative = Array.isArray(timeline.cumulative) ? timeline.cumulative : [];

            timelineChart.data.labels = labels;
            timelineChart.data.datasets[0].data = values;
            timelineChart.data.datasets[1].data = cumulative;
            timelineChart.update();
        };

        $("#client-conversion-timeline-table").appTable({
            source: '<?php echo_uri("lead_conversion_reports/client_timeline"); ?>',
            filterDropdown: [
                {name: "owner_id", class: "w200", options: <?php echo $owners_dropdown; ?>},
                {name: "region_id", class: "w200", options: <?php echo $regions_dropdown; ?>},
                {name: "source_value", class: "w200", options: <?php echo $sources_dropdown; ?>},
                {name: "lead_status_id", class: "w200", options: <?php echo $statuses_dropdown; ?>}
            ],
            rangeDatepicker: [
                {startDate: {name: "migration_start_date", value: ""}, endDate: {name: "migration_end_date", value: ""}, label: "<?php echo app_lang('conversion_date'); ?>", showClearButton: true}
            ],
            columns: [
                {title: "<?php echo app_lang('client_name'); ?>", class: "all"},
                {title: "<?php echo app_lang('conversion_date'); ?>", class: "w15p"},
                {title: "<?php echo app_lang('owner'); ?>", class: "w15p"},
                {title: "<?php echo app_lang('region'); ?>", class: "w15p"},
                {title: "<?php echo app_lang('campaign'); ?>", class: "w15p"},
                {title: "<?php echo app_lang('lead_status'); ?>", class: "w15p"}
            ],
            order: [[1, "asc"]]
        });

        $("#client-conversion-timeline-table").on("xhr.dt", function (e, settings, json) {
            if (json && json.timeline) {
                updateTimelineChart(json.timeline);
            }
        });

        $("#download-client-conversion-timeline").on("click", function () {
            var button = this;
            button.style.display = 'none';
            html2canvas(document.getElementById('client-conversion-timeline-chart-container')).then(function (canvas) {
                var imgData = canvas.toDataURL('image/png');
                var pdf = new jspdf.jsPDF();
                var width = pdf.internal.pageSize.getWidth();
                var height = canvas.height * width / canvas.width;
                pdf.addImage(imgData, 'PNG', 0, 0, width, height);
                pdf.save('client-conversion-timeline.pdf');
                button.style.display = '';
            });
        });
    });
</script>
