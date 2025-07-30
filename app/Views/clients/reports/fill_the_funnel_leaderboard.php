<?php echo get_reports_topbar(); ?>

<!-- Make sure jQuery is available before executing page scripts -->
<script src="<?php echo base_url('assets/js/jquery-3.5.1.min.js'); ?>"></script>

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
