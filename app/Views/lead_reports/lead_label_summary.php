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
        $("#lead-label-summary-table").appTable({
            source: '<?php echo_uri("lead_reports/lead_label_summary_list"); ?>',
            filterDropdown: [
                {name: "source_value", class: "w200", options: <?php echo $sources_dropdown; ?>}
            ],
            rangeDatepicker: [
                {startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, label: "<?php echo app_lang('created_date'); ?>", showClearButton: true}
            ],
            columns: [
                {title: "<?php echo app_lang('label'); ?>", class: "all"},
                {title: "<?php echo app_lang('total_leads'); ?>", class: "text-right"},
                {title: "<?php echo app_lang('all_time'); ?>", class: "text-right"}
            ],
            order: [[1, "desc"]],
            printColumns: [0, 1, 2],
            xlsColumns: [0, 1, 2]
        });
    });
</script>
