<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="table-responsive">
            <table id="fill-the-funnel-leaderboard" class="display" width="100%"></table>
        </div>
    </div>
    <div class="card clearfix mt20">
        <div class="table-responsive">
            <table id="fill-the-funnel-region-leaderboard" class="display" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#fill-the-funnel-leaderboard").appTable({
            source: '<?php echo_uri("clients/fill_the_funnel_leaderboard_data") ?>',
            rangeDatepicker: [{startDate: {name: "start_date", value: "2025-07-21"}, endDate: {name: "end_date", value: "2025-09-30"}, showClearButton: true}],
            columns: [
                {title: '<?php echo app_lang("sales_rep_name"); ?>', class: "all"},
                {title: '<?php echo app_lang("roc"); ?>'},
                {title: '<?php echo app_lang("new_opportunities"); ?>', class: "text-center"},
                {title: '<?php echo app_lang("closed_deals"); ?>', class: "text-center"},
                {title: '<?php echo app_lang("total_points"); ?>', class: "text-center"}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });

        $("#fill-the-funnel-region-leaderboard").appTable({
            source: '<?php echo_uri("clients/fill_the_funnel_region_leaderboard_data") ?>',
            rangeDatepicker: [{startDate: {name: "start_date", value: "2025-07-21"}, endDate: {name: "end_date", value: "2025-09-30"}, showClearButton: true}],
            columns: [
                {title: '<?php echo app_lang("region"); ?>'},
                {title: '<?php echo app_lang("new_opportunities"); ?>', class: "text-center"},
                {title: '<?php echo app_lang("closed_deals"); ?>', class: "text-center"},
                {title: '<?php echo app_lang("total_points"); ?>', class: "text-center"}
            ],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });
    });
</script>