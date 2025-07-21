<div class="table-responsive">
    <table id="team-members-summary" class="display" width="100%">
    </table>
</div>

<?php
$columns = array(array("title" => app_lang("owner"), "class" => "all"));
foreach ($lead_statuses as $status) {
    $columns[] = array("title" => $status->title, "class" => "text-right");
}
$columns[] = array("title" => "Won %", "class" => "text-right all");

$total_columns = count($columns);
$print_columns = range(0, $total_columns - 1);
?>

<script type="text/javascript">

    $(document).ready(function () {

        $("#team-members-summary").appTable({

            source: '<?php echo_uri("leads/team_members_summary_data") ?>',
            rangeDatepicker: [{startDate: {name: "created_date_from", value: ""}, endDate: {name: "created_date_to", value: ""}, showClearButton: true, label: "<?php echo app_lang('created_date'); ?>", ranges: ['this_month', 'last_month', 'this_year', 'last_year', 'last_30_days', 'last_7_days']}],
            filterDropdown: [
                {name: "source_id", class: "w200", options: <?php echo $sources_dropdown; ?>},
                {name: "label_id", class: "w200", options: <?php echo $labels_dropdown; ?>}
            ],
            columns: <?php echo json_encode($columns) ?>,
            printColumns: <?php echo json_encode($print_columns); ?>,
            xlsColumns: <?php echo json_encode($print_columns); ?>
        });
    }
    );
</script>