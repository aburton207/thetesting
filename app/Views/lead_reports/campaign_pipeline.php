<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="p20">
            <h4 class="card-title"><?php echo app_lang("campaign_pipeline_summary"); ?></h4>
            <div class="row mb15">
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="campaign-filter" class="form-label"><?php echo app_lang("campaign"); ?></label>
                        <select id="campaign-filter" class="form-control select2" multiple="multiple"></select>
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
                <table id="campaign-assignment-table" class="display" width="100%"></table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var campaignOptions = <?php echo $campaigns_dropdown; ?> || [];
        var statusColumns = <?php echo $campaign_status_columns; ?> || [];
        var initialCampaignSelection = <?php echo $selected_campaigns; ?> || [];
        var $campaignFilter = $("#campaign-filter");
        var selectedCampaigns = [];
        var placeholderText = "- <?php echo app_lang('campaign'); ?> -";
        var formatNumber = function (value) {
            var numericValue = parseFloat(value);
            if (isNaN(numericValue)) {
                numericValue = 0;
            }

            if (window.AppHelper && $.isFunction(AppHelper.numberFormat)) {
                return AppHelper.numberFormat(numericValue);
            }

            return numericValue.toLocaleString();
        };

        if (campaignOptions.length) {
            $.each(campaignOptions, function (index, option) {
                if (!option.id) {
                    placeholderText = option.text || placeholderText;
                    return;
                }

                var $option = $("<option />").val(option.id).text(option.text);
                if (option.isSelected || $.inArray(option.id, initialCampaignSelection) !== -1) {
                    $option.prop("selected", true);
                }
                $campaignFilter.append($option);
            });
        }

        $campaignFilter.select2({
            placeholder: placeholderText,
            allowClear: true
        });

        selectedCampaigns = $campaignFilter.val() || [];

        var summaryColumns = [{title: "<?php echo app_lang('campaign'); ?>", "class": "all"}];
        var summaryColumnIndexes = [0];
        var summarySummation = [];

        if (statusColumns.length) {
            $.each(statusColumns, function (index, column) {
                summaryColumns.push({title: column.title, "class": "text-right"});
                summaryColumnIndexes.push(summaryColumnIndexes.length);
                summarySummation.push({column: summaryColumnIndexes.length - 1, dataType: 'number'});
            });
        }

        summaryColumns.push({title: "<?php echo app_lang('total'); ?>", "class": "text-right"});
        summaryColumnIndexes.push(summaryColumnIndexes.length);
        summarySummation.push({column: summaryColumnIndexes.length - 1, dataType: 'number'});

        var $summaryTable = $("#campaign-pipeline-summary-table");

        $summaryTable.appTable({
            source: '<?php echo_uri("lead_reports/campaign_pipeline_summary_list"); ?>',
            filterParams: {campaign: JSON.stringify(selectedCampaigns)},
            columns: summaryColumns,
            order: [],
            printColumns: summaryColumnIndexes,
            xlsColumns: summaryColumnIndexes,
            summation: summarySummation
        });

        var $assignmentTable = $("#campaign-assignment-table");

        $assignmentTable.appTable({
            source: '<?php echo_uri("lead_reports/campaign_pipeline_breakdown_list"); ?>',
            filterParams: {campaign: JSON.stringify(selectedCampaigns)},
            columns: [
                {title: "<?php echo app_lang('campaign'); ?>", "class": "all"},
                {title: "<?php echo app_lang('assigned_reps'); ?>", "class": "text-right"},
                {title: "<?php echo app_lang('unassigned_reps'); ?>", "class": "text-right"},
                {title: "<?php echo app_lang('total'); ?>", "class": "text-right"}
            ],
            order: [],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3],
            summation: [
                {column: 1, dataType: 'number'},
                {column: 2, dataType: 'number'},
                {column: 3, dataType: 'number'}
            ]
        });

        $campaignFilter.on("change", function () {
            selectedCampaigns = $(this).val() || [];
            $summaryTable.appTable({reload: true, filterParams: {campaign: JSON.stringify(selectedCampaigns)}});
            $assignmentTable.appTable({reload: true, filterParams: {campaign: JSON.stringify(selectedCampaigns)}});
        });
    });
</script>
