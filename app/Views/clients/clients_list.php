<div class="card border-top-0 rounded-top-0">
    <div class="table-responsive">
        <table id="client-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    loadClientsTable = function (selector) {
        var showInvoiceInfo = true;
        if (!"<?php echo $show_invoice_info; ?>") {
            showInvoiceInfo = false;
        }

        var showOptions = true;
        if (!"<?php echo $can_edit_clients; ?>") {
            showOptions = false;
        }

        var ignoreSavedFilter = false;
        var quick_filters_dropdown = <?php echo view("clients/quick_filters_dropdown"); ?>;
        var type_dropdown = [
            {id: "", text: "- <?php echo app_lang('type'); ?> -"},
            {id: "person", text: "<?php echo app_lang('person'); ?>"},
            {id: "organization", text: "<?php echo app_lang('organization'); ?>"}
        ];
        if (window.selectedClientQuickFilter) {
            var filterIndex = quick_filters_dropdown.findIndex(x => x.id === window.selectedClientQuickFilter);
            if (filterIndex > -1) {
                ignoreSavedFilter = true;
                quick_filters_dropdown[filterIndex].isSelected = true;
            }
        }

        // Define base columns
        var columns = [
            {title: "<?php echo app_lang('id') ?>", "class": "text-center w50 desktop", order_by: "id"},
            {title: "<?php echo app_lang('name') ?>", "class": "all", order_by: "company_name"},
            {title: "<?php echo app_lang('primary_contact') ?>", order_by: "primary_contact"},
            {title: "<?php echo app_lang('phone') ?>", order_by: "phone"},
            {title: "<?php echo app_lang('type') ?>", order_by: "account_type"},
              {title: "Created Date", order_by: "created_date"},
            {title: "<?php echo app_lang('client_groups') ?>", order_by: "client_groups"},
            {title: "<?php echo app_lang('owner') ?>", order_by: "client_owner"},
            {title: "<?php echo app_lang('source') ?>", order_by: "lead_source_title"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang('total_invoiced') ?>", "class": "text-right"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang('payment_received') ?>", "class": "text-right"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang('due') ?>", "class": "text-right"},
            {title: "<?php echo app_lang('status') ?>", "class": "text-center w100", order_by: "status"},
            {title: "Probability %", "class": "text-center w100"},
            {title: "Potential Margin", "class": "text-right w100"},
            {title: "Weighted Forecast", "class": "text-right w100"}
        ];

        // Add custom field headers if defined
        <?php if (!empty($custom_field_headers)) { ?>
            try {
                var customColumns = [<?php echo $custom_field_headers; ?>];
                columns = columns.concat(customColumns.filter(function(col) { return col && typeof col === 'object'; }));
            } catch (e) {
                console.error('Invalid custom field headers:', e);
            }
        <?php } ?>

        // Add options column
        columns.push({title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOptions});

        $(selector).appTable({
            source: '<?php echo_uri("clients/list_data") ?>',
            serverSide: true,
            smartFilterIdentity: "all_clients_list",
            ignoreSavedFilter: ignoreSavedFilter,
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
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], '<?php echo $custom_field_headers; ?>'),
            ajax: {
                dataSrc: function (data) {
                    console.log('Table Data:', data);
                    return data.data;
                }
            }
        });
    };

    // Handle status update clicks
    $('body').on('click', '[data-act=update-lead-status]', function () {
        var $element = $(this),
            recordId = $element.attr('data-id'),
            isClientTable = $element.closest('#client-table').length > 0,
            tableId = isClientTable ? '#client-table' : '#lead-table',
            actionUrl = isClientTable 
                ? '<?php echo_uri("clients/save_client_status") ?>/' + recordId 
                : '<?php echo_uri("leads/save_lead_status") ?>/' + recordId;

        $element.appModifier({
            value: $element.attr('data-value'),
            actionUrl: actionUrl,
            select2Option: {data: <?php echo isset($statuses) ? json_encode(array_map(function($status) {
                return array("id" => $status->id, "text" => $status->title);
            }, $statuses)) : '[]'; ?>},
            onSuccess: function (response, newValue) {
                if (response.success) {
                    $(tableId).appTable({newData: response.data, dataId: response.id});
                }
            }
        });

        return false;
    });

    $(document).ready(function () {
        loadClientsTable("#client-table");
    });
</script>