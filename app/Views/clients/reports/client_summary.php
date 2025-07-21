<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="table-responsive">
            <table id="clients-report-table" class="display" width="100%"></table>
            <div id="clients-summary" class="p15"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var showInvoiceInfo = <?php echo json_encode($show_invoice_info); ?>;
        var showOptions = <?php echo json_encode($can_edit_clients); ?>;
        var type_dropdown = [
            {id: "", text: "- <?php echo app_lang('type'); ?> -"},
            {id: "person", text: "<?php echo app_lang('person'); ?>"},
            {id: "organization", text: "<?php echo app_lang('organization'); ?>"}
        ];

        var columns = [
            {title: "<?php echo app_lang('id'); ?>", class: "text-center w50 desktop", order_by: "id"},
            {title: "<?php echo app_lang('name'); ?>", class: "all", order_by: "company_name"},
            {title: "<?php echo app_lang('type'); ?>", order_by: "account_type"},
            {title: "Created Date", order_by: "created_date"},
            {title: "<?php echo app_lang('client_groups'); ?>", order_by: "client_groups"},
            {title: "<?php echo app_lang('owner'); ?>", order_by: "client_owner"},
            {title: "<?php echo app_lang('source'); ?>", order_by: "lead_source_title"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang('total_invoiced'); ?>", class: "text-right"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang('payment_received'); ?>", class: "text-right"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang('due'); ?>", class: "text-right"},
            {title: "<?php echo app_lang('status'); ?>", class: "text-center w100", order_by: "status"},
            {title: "Probability %", class: "text-center w100"},
            {title: "Potential Margin", class: "text-right w100"},
            {title: "Weighted Forecast", class: "text-right w100"}
        ];

        <?php if (!empty($custom_field_headers)) { ?>
        try {
            var customColumns = [<?php echo $custom_field_headers; ?>];
            columns = columns.concat(customColumns.filter(function (col) { return col && typeof col === 'object'; }));
        } catch (e) { }
        <?php } ?>

        columns.push({title: '<i data-feather="menu" class="icon-16"></i>', class: "text-center option w100", visible: showOptions});

        //prepare summation options to show totals row and export summary
        var summation = [
            {column: 11, fieldName: "avg_probability", dataType: 'number'},
            {column: 12, fieldName: "sum_potential_margin", dataType: 'currency', dynamicSymbol: true},
            {column: 13, fieldName: "sum_weighted_forecast", dataType: 'currency', dynamicSymbol: true},
            {column: 14, fieldName: "sum_volume", dataType: 'number'},
            {column: 15, fieldName: "avg_margin_above_rack", dataType: 'number'}
        ];

        var updateSummary = function (info) {
            if (!info) return;
            var html = "<strong>Average Probability:</strong> " + parseFloat(info.avg_probability).toFixed(2) + "% ";
            html += "&nbsp;&nbsp;<strong>Sum Potential Margin:</strong> " + toCurrency(info.sum_potential_margin);
            html += "&nbsp;&nbsp;<strong>Sum Weighted Forecast:</strong> " + toCurrency(info.sum_weighted_forecast);
            html += "&nbsp;&nbsp;<strong>Sum Volume:</strong> " + parseFloat(info.sum_volume).toFixed(2);
            html += "&nbsp;&nbsp;<strong>Average Margin Above Rack:</strong> " + parseFloat(info.avg_margin_above_rack).toFixed(2);
            $("#clients-summary").html(html);
        };

        var quick_filters_dropdown = <?php echo view("clients/quick_filters_dropdown"); ?>;
        $("#clients-report-table").appTable({
            source: '<?php echo_uri("clients/clients_report_list_data") ?>',
            filterDropdown: [
                {name: "quick_filter", class: "w200", options: quick_filters_dropdown},
                <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
                    {name: "owner_id", class: "w200", options: <?php echo $team_members_dropdown; ?>},
                <?php } ?>
                {name: "group_id", class: "w200", options: <?php echo $groups_dropdown; ?>},
                {name: "account_type", class: "w200", options: type_dropdown},
                {name: "status", class: "w200", options: <?php echo view("clients/client_statuses"); ?>},
                {name: "source_id", class: "w200", options: <?php echo view("leads/lead_sources", array("lead_sources" => $lead_sources)); ?>},
                <?php echo $custom_field_filters; ?>
            ],
            rangeDatepicker: [
                {startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, label: "<?php echo app_lang('created_date'); ?>", showClearButton: true},
                {startDate: {name: "estimated_close_start_date", value: ""}, endDate: {name: "estimated_close_end_date", value: ""}, label: "Estimated Close", showClearButton: true}
            ],
            columns: columns,
            printColumns: combineCustomFieldsColumns([0,1,2,3,4,5,6,7,8,9,10,11,12,13], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0,1,2,3,4,5,6,7,8,9,10,11,12,13], '<?php echo $custom_field_headers; ?>'),
            summation: summation,
            onInitComplete: function (instance) {
                var info = null;
                if (instance && typeof instance.settings === "function") {
                    info = instance.settings()[0].oInit.summationInfo;
                } else if (instance && instance.settings) {
                    info = instance.settings.summationInfo;
                }
                updateSummary(info);
            },
            onRelaodCallback: function (instance) {
                var info = null;
                if (instance && typeof instance.settings === "function") {
                    info = instance.settings()[0].oInit.summationInfo;
                } else if (instance && instance.settings) {
                    info = instance.settings.summationInfo;
                }
                updateSummary(info);
            },
            footerCallback: function (row, data, start, end, display, table) {
                var api = new $.fn.dataTable.Api(table);
                var dt = api;

                //calculate page average for probability column
                var probData = api.column(11, {page: 'current'}).data();
                var probSum = 0;
                probData.each(function(value) {
                    var n = parseFloat(value);
                    if (!isNaN(n)) { probSum += n; }
                });
                var avgProb = probData.length ? probSum / probData.length : 0;
                $(dt.table().footer()).find('[data-current-page="11"]').html(avgProb.toFixed(2) + "%");

                //calculate page average for margin above rack column
                var marData = api.column(15, {page: 'current'}).data();
                var marSum = 0;
                marData.each(function(value) {
                    var n = parseFloat(value);
                    if (!isNaN(n)) { marSum += n; }
                });
                var avgMar = marData.length ? marSum / marData.length : 0;
                $(dt.table().footer()).find('[data-current-page="15"]').html(avgMar.toFixed(2));

                updateSummary(dt.settings()[0].oInit.summationInfo);
            }
        });
    });
</script>
