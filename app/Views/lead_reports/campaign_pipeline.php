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
            <div id="campaign-pipeline-breakdown-wrapper" class="clearfix"></div>
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

        var $breakdownWrapper = $("#campaign-pipeline-breakdown-wrapper");

        var loadBreakdown = function () {
            var requestData = {campaign: JSON.stringify(selectedCampaigns)};

            $breakdownWrapper.find('table').each(function () {
                if ($.fn.DataTable && $.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().destroy();
                }
            });

            $breakdownWrapper.empty();

            appLoader.show();

            $.ajax({
                url: '<?php echo_uri("lead_reports/campaign_pipeline_breakdown_list"); ?>',
                type: 'POST',
                dataType: 'json',
                data: requestData,
                success: function (response) {
                    appLoader.hide();

                    if (response && response.status_definitions) {
                        statusColumns = response.status_definitions;
                    }

                    var campaigns = (response && response.campaigns) ? response.campaigns : [];

                    if (!campaigns.length) {
                        $breakdownWrapper.append('<div class="text-center text-off p20">' + appLang('no_data_found') + '</div>');
                        return;
                    }

                    $.each(campaigns, function (index, campaign) {
                        var tableId = 'campaign-breakdown-table-' + index;
                        var $card = $('<div class="card clearfix mb15"></div>');
                        var $cardBody = $('<div class="p20"></div>').appendTo($card);
                        $('<h5 class="card-title"></h5>').text(campaign.label || '').appendTo($cardBody);

                        var $tableWrapper = $('<div class="table-responsive"></div>').appendTo($cardBody);
                        var $table = $('<table class="display" width="100%"></table>').attr('id', tableId).appendTo($tableWrapper);

                        var columns = [{title: "<?php echo app_lang('owner'); ?>", className: 'all'}];
                        var exportColumns = [0];
                        var tableData = [];

                        if (statusColumns.length) {
                            $.each(statusColumns, function (statusIndex, column) {
                                columns.push({title: column.title, className: 'text-right'});
                                exportColumns.push(exportColumns.length);
                            });
                        }

                        columns.push({title: "<?php echo app_lang('total'); ?>", className: 'text-right'});
                        exportColumns.push(exportColumns.length);

                        var owners = campaign.owners || [];
                        if (owners.length) {
                            $.each(owners, function (ownerIndex, owner) {
                                var row = [owner.owner_name || appLang('not_specified')];

                                if (statusColumns.length) {
                                    $.each(statusColumns, function (statusIndex, column) {
                                var counts = owner.counts || {};
                                var value = counts.hasOwnProperty(column.key) ? counts[column.key] : 0;
                                row.push(formatNumber(value));
                            });
                        }

                        row.push(formatNumber(owner.total || 0));
                        tableData.push(row);
                    });
                }

                var totals = campaign.totals || {counts: {}};
                var $tfootRow = $('<tr></tr>');
                $tfootRow.append($('<th></th>').text(appLang('total')));

                if (statusColumns.length) {
                    $.each(statusColumns, function (statusIndex, column) {
                        var value = (totals.counts && totals.counts.hasOwnProperty(column.key)) ? totals.counts[column.key] : 0;
                        $tfootRow.append($('<th class="text-right"></th>').text(formatNumber(value)));
                    });
                }

                $tfootRow.append($('<th class="text-right"></th>').text(formatNumber(totals.total || 0)));

                        var $tfoot = $('<tfoot></tfoot>').append($tfootRow);
                        $table.append($tfoot);

                        $breakdownWrapper.append($card);

                        if ($.fn.DataTable) {
                            $table.DataTable({
                                data: tableData,
                                columns: columns,
                                paging: false,
                                searching: false,
                                ordering: false,
                                info: false,
                                dom: 'Bfrtip',
                                buttons: [
                                    {
                                        extend: 'excel',
                                        text: "<?php echo app_lang('export_to_excel'); ?>",
                                        title: (campaign.label || '') + ' - <?php echo app_lang("campaign_pipeline_breakdown"); ?>',
                                        exportOptions: {
                                            columns: exportColumns
                                        }
                                    }
                                ]
                            });
                        }
                    });
                },
                error: function () {
                    appLoader.hide();
                    $breakdownWrapper.append('<div class="text-center text-danger p20">' + appLang('something_went_wrong') + '</div>');
                }
            });
        };

        loadBreakdown();

        $campaignFilter.on("change", function () {
            selectedCampaigns = $(this).val() || [];
            $summaryTable.appTable({reload: true, filterParams: {campaign: JSON.stringify(selectedCampaigns)}});
            loadBreakdown();
        });
    });
</script>
