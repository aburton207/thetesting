<?php if ($login_user->user_type == "staff") { ?>
    <div class="card rounded-top-0">
        <div class="tab-title clearfix">
            <h4><?php echo app_lang('estimate_requests'); ?></h4>
        </div>
    <?php } ?>

    <div class="table-responsive">
        <table id="estimate-request-table" class="display" cellspacing="0" width="100%">
        </table>
    </div>
    <?php if ($login_user->user_type == "staff") { ?>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function () {

        var fieldVisibility = false;
        if ("<?php echo $login_user->user_type; ?>" === "staff") {
            fieldVisibility = true;
        }

        $("#estimate-request-table").appTable({
            source: '<?php echo_uri("estimate_requests/estimate_requests_list_data_of_client/" . $client_id) ?>',
            order: [[0, 'desc']],
            rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, label: "<?php echo app_lang('created_date'); ?>", showClearButton: true}],
            columns: [
                {title: "<?php echo app_lang('id'); ?>", "class": "all"},
                {visible: false, searchable: false},
                {title: "<?php echo app_lang('title'); ?>"},
                {title: "<?php echo app_lang('assigned_to'); ?>", visible: fieldVisibility},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("created_date") ?>', "iDataSort": 3},
                {title: "<?php echo app_lang('status'); ?>"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center dropdown-option w100", visible: fieldVisibility}
            ],
            printColumns: [0, 2, 3, 5, 6]
        });
    });
</script>