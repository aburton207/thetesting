<div id="page-content" class="page-wrapper clearfix">
    <div class="page-title clearfix">
        <div class="title-button-group">
            <?php echo anchor(get_uri("clients"), "<i data-feather='list' class='icon-16'></i> " . app_lang('show_normal_view'), array("class" => "btn btn-default", "title" => app_lang('show_normal_view'))); ?>
        </div>
    </div>
    <div class="card border-top-0 rounded-top-0">
        <div class="table-responsive">
            <table id="clients-expanded-table" class="display" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var showOptions = <?php echo json_encode($can_edit_clients); ?>;
        var quick_filters_dropdown = <?php echo view("clients/quick_filters_dropdown"); ?>;
        var type_dropdown = [
            {id: "", text: "- <?php echo app_lang('type'); ?> -"},
            {id: "person", text: "<?php echo app_lang('person'); ?>"},
            {id: "organization", text: "<?php echo app_lang('organization'); ?>"}
        ];

        var columns = [
            {title: "<?php echo app_lang('id'); ?>", class: "text-center w50 desktop", order_by: "id"},
            {title: "<?php echo app_lang('name'); ?>", class: "all", order_by: "company_name"},
            {title: "<?php echo app_lang('primary_contact'); ?>", order_by: "primary_contact"},
            {title: "<?php echo app_lang('address'); ?>", order_by: "address"},
            {title: "<?php echo app_lang('city'); ?>", order_by: "city"},
            {title: "<?php echo app_lang('state'); ?>", order_by: "state"},
            {title: "<?php echo app_lang('zip'); ?>", order_by: "zip"},
            {title: "<?php echo app_lang('phone'); ?>", order_by: "phone"},
            {title: "<?php echo app_lang('type'); ?>", order_by: "account_type"},
            {title: "Created Date", order_by: "created_date"},
            {title: "<?php echo app_lang('client_groups'); ?>", order_by: "client_groups"},
            {title: "<?php echo app_lang('owner'); ?>", order_by: "client_owner"},
            {title: "<?php echo app_lang('source'); ?>", order_by: "lead_source_title"},
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

        $("#clients-expanded-table").appTable({
            source: '<?php echo_uri("clients/show_expanded_view_list_data") ?>',
            serverSide: true,
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
                {startDate: {name: "estimated_close_start_date", value: ""}, endDate: {name: "estimated_close_end_date", value: ""}, label: "Estimated Close", showClearButton: true},
                {startDate: {name: "closed_start_date", value: ""}, endDate: {name: "closed_end_date", value: ""}, label: "Closed Date", showClearButton: true}
            ],
            columns: columns,
            printColumns: combineCustomFieldsColumns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16], '<?php echo $custom_field_headers; ?>')
        });
    });
</script>
