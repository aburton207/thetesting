<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="table-responsive">
            <table id="lead-label-summary-table" class="display" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var $leadLabelSummaryTable = $("#lead-label-summary-table");
        var dateRangeElement = "<span id=\"lead-label-date-range\"></span>";

        $leadLabelSummaryTable.appTable({
            source: '<?php echo_uri("lead_reports/lead_label_summary_list"); ?>',
            filterDropdown: [
                {name: "source_value", class: "w200", options: <?php echo $sources_dropdown; ?>}
            ],
            rangeDatepicker: [
                {startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, label: "<?php echo app_lang('created_date'); ?>", showClearButton: true}
            ],
            columns: [
                {title: "<?php echo app_lang('label'); ?>", class: "all"},
                {title: "<?php echo app_lang('total_leads'); ?> " + dateRangeElement, class: "text-right"},
                {title: "<?php echo app_lang('all_time'); ?>", class: "text-right"}
            ],
            order: [[1, "desc"]],
            printColumns: [0, 1, 2],
            xlsColumns: [0, 1, 2],
            summation: [
                {column: 1, dataType: 'number'},
                {column: 2, dataType: 'number'}
            ]
        });

        var $dateRangeDisplay = $("#lead-label-date-range");

        function updateDateRangeLabel(dateRangeText) {
            if (dateRangeText) {
                $dateRangeDisplay.text("(" + dateRangeText + ")");
            } else {
                $dateRangeDisplay.text("");
            }
        }

        updateDateRangeLabel("");

        $leadLabelSummaryTable.on("xhr.dt", function (e, settings, json) {
            if (json && json.date_range_label) {
                updateDateRangeLabel(json.date_range_label);
            } else {
                updateDateRangeLabel("");
            }
        });
    });
</script>
