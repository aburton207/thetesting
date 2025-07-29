<div id="page-content" class="page-wrapper clearfix full-width-button">
    <div class="card estimate-request-full-button">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('estimate_requests'); ?></h1>

            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php echo modal_anchor(get_uri("estimate_requests/request_an_estimate_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('create_estimate_request'), array("class" => "btn btn-default", "title" => app_lang('create_estimate_request'))); ?>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="estimate-request-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#estimate-request-table").appTable({
            source: '<?php echo_uri("estimate_requests/estimate_request_list_data") ?>',
            order: [[14, 'desc']],
            filterDropdown: [
                {name: "assigned_to", class: "w150", options: <?php echo $assigned_to_dropdown; ?>},
                {name: "status", class: "w150", options: <?php echo $statuses_dropdown; ?>},
                {name: "form_id", class: "w150", options: <?php echo $forms_dropdown; ?>}
            ],
            rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, label: "<?php echo app_lang('created_date'); ?>", showClearButton: true}],
            columns: [
                {title: "<?php echo app_lang('id'); ?>", "class": "all"},
                {title: "<?php echo app_lang('client'); ?>", "class": "all"},
                {title: "<?php echo app_lang('title'); ?>"},
                {title: "<?php echo app_lang('address'); ?>"},
                {title: "<?php echo app_lang('state'); ?>"},
                {title: "<?php echo app_lang('zip'); ?>"},
                {title: "<?php echo app_lang('phone'); ?>"},
                {title: "Account #"},
                {title: "Auto Delivery"},
                {title: "Monthly Autopay"},
                {title: "Bank Payment"},
                {title: "e-Invoice"},
                {title: "Additional Info"},
                {title: "Custom 275"},
                {title: "<?php echo app_lang('assigned_to'); ?>"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("created_date") ?>', "iDataSort": 15},
                {title: "<?php echo app_lang('status'); ?>"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center dropdown-option w50"}
            ],
            printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 16, 17],
            xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 16, 17]
        });
    });
</script>