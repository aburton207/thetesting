<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="table-responsive">
            <table id="lead-conversion-table" class="display" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#lead-conversion-table").appTable({
            source: '<?php echo_uri("lead_conversion_reports/data"); ?>',
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
                {title: "<?php echo app_lang('source'); ?>", class: "all"},
                {title: "<?php echo app_lang('owner'); ?>"},
                {title: "<?php echo app_lang('region'); ?>"},
                {title: "<?php echo app_lang('total_leads'); ?>", class: "text-center"},
                {title: "<?php echo app_lang('converted_to_client'); ?>", class: "text-center"},
                {title: "<?php echo app_lang('conversion_rate'); ?>", class: "text-right"},
                {title: "<?php echo app_lang('average_conversion_time'); ?>", class: "text-right"}
            ],
            order: [[3, "desc"]],
            printColumns: [0, 1, 2, 3, 4, 5, 6],
            xlsColumns: [0, 1, 2, 3, 4, 5, 6]
        });
    });
</script>
