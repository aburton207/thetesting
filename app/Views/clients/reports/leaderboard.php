<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="table-responsive">
            <table id="clients-leaderboard" class="display" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#clients-leaderboard").appTable({
            source: '<?php echo_uri("clients/leaderboard_data"); ?>',
            filterDropdown: [
                {name: "owner_id", class: "w200", options: <?php echo $team_members_dropdown; ?>},
                {name: "role_id", class: "w200", options: <?php echo $roles_dropdown; ?>},
                {name: "roc", class: "w200", options: <?php echo $roc_dropdown; ?>}
            ],
            rangeDatepicker: [
                {startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, label: "<?php echo app_lang('closed_date'); ?>", showClearButton: true}
            ],
            columns: [
                {title: "<?php echo app_lang('sales_rep_name'); ?>", class: "all"},
                {title: "<?php echo app_lang('role'); ?>"},
                {title: "<?php echo app_lang('roc'); ?>"},
                {title: "<?php echo app_lang('closed_won_opportunities'); ?>", class: "text-center"},
                {title: "<?php echo app_lang('total_volume'); ?>", class: "text-right"},
                {title: "<?php echo app_lang('total_margin'); ?>", class: "text-right"}
            ],
            order: [[3, "desc"]],
            printColumns: [0, 1, 2, 3, 4, 5],
            xlsColumns: [0, 1, 2, 3, 4, 5]
        });
    });
</script>
