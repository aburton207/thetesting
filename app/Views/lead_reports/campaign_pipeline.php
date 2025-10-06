<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="p20">
            <h4 class="card-title"><?php echo app_lang("campaign_pipeline_summary"); ?></h4>
            <div class="row mb15">
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label for="campaign-filter" class="form-label"><?php echo app_lang("campaign"); ?></label>
                        <select id="campaign-filter" class="form-control select2"></select>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="campaign-pipeline-summary-table" class="display" width="100%"></table>
            </div>
        </div>
    </div>

    <div class="card clearfix mt20">
        <div class="p20">
            <h4 class="card-title"><?php echo app_lang("campaign_pipeline_breakdown"); ?></h4>
            <div class="table-responsive">
                <table id="campaign-pipeline-breakdown-table" class="display" width="100%"></table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var campaignOptions = <?php echo $campaigns_dropdown; ?> || [];
        var $campaignFilter = $("#campaign-filter");
        var selectedCampaign = "";

        if (campaignOptions.length) {
            $.each(campaignOptions, function (index, option) {
                var $option = $("<option />").val(option.id).text(option.text);
                if (option.isSelected) {
                    $option.prop("selected", true);
                    selectedCampaign = option.id;
                }
                $campaignFilter.append($option);
            });
        }

        $campaignFilter.select2();

        if (!selectedCampaign) {
            selectedCampaign = $campaignFilter.val() || "";
        }

        var $summaryTable = $("#campaign-pipeline-summary-table");
        var $breakdownTable = $("#campaign-pipeline-breakdown-table");

        $summaryTable.appTable({
            source: '<?php echo_uri("lead_reports/campaign_pipeline_summary_list"); ?>',
            filterParams: {campaign: selectedCampaign},
            columns: [
                {title: "<?php echo app_lang('status'); ?>", "class": "all"},
                {title: "<?php echo app_lang('leads'); ?>", "class": "text-right"},
                {title: "<?php echo app_lang('clients'); ?>", "class": "text-right"},
                {title: "<?php echo app_lang('total'); ?>", "class": "text-right"}
            ],
            order: [[0, "asc"]],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3],
            summation: [
                {column: 1, dataType: 'number'},
                {column: 2, dataType: 'number'},
                {column: 3, dataType: 'number'}
            ]
        });

        $breakdownTable.appTable({
            source: '<?php echo_uri("lead_reports/campaign_pipeline_breakdown_list"); ?>',
            filterParams: {campaign: selectedCampaign},
            order: [[0, "asc"], [1, "asc"], [2, "asc"]],
            columns: [
                {title: "<?php echo app_lang('campaign'); ?>", "class": "all"},
                {title: "<?php echo app_lang('owner'); ?>", "class": "all"},
                {title: "<?php echo app_lang('status'); ?>", "class": "all"},
                {title: "<?php echo app_lang('leads'); ?>", "class": "text-right"},
                {title: "<?php echo app_lang('clients'); ?>", "class": "text-right"},
                {title: "<?php echo app_lang('total'); ?>", "class": "text-right"}
            ],
            printColumns: [0, 1, 2, 3, 4, 5],
            xlsColumns: [0, 1, 2, 3, 4, 5],
            summation: [
                {column: 3, dataType: 'number'},
                {column: 4, dataType: 'number'},
                {column: 5, dataType: 'number'}
            ]
        });

        $campaignFilter.on("change", function () {
            selectedCampaign = $(this).val() || "";
            $summaryTable.appTable({reload: true, filterParams: {campaign: selectedCampaign}});
            $breakdownTable.appTable({reload: true, filterParams: {campaign: selectedCampaign}});
        });
    });
</script>
