<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="table-responsive">
            <table id="fill-the-funnel-leaderboard" class="display" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#fill-the-funnel-leaderboard").appTable({
            source: '<?php echo_uri("clients/fill_the_funnel_leaderboard_data") ?>',
            rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, showClearButton: true}],
            columns: [
                {title: '<?php echo app_lang("sales_rep_name"); ?>', class: "all"},
                {title: '<?php echo app_lang("roc"); ?>'},
                {title: '<?php echo app_lang("new_opportunities"); ?>', class: "text-center"},
                {title: '<?php echo app_lang("closed_deals"); ?>', class: "text-center"},
                {title: '<?php echo app_lang("total_points"); ?>', class: "text-center"}
            ]
        });
    });
</script>
