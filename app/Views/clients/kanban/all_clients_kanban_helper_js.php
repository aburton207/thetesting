<script type="text/javascript">

    $(document).ready(function () {
        var scrollLeft = 0;
        $("#kanban-filters").appFilters({
            source: '<?php echo_uri("clients/all_clients_kanban_data") ?>',
            targetSelector: '#load-kanban',
            reloadSelector: "#reload-kanban-button",
            smartFilterIdentity: "all_clients_kanban", //a to z and _ only. should be unique to avoid conflicts
            search: {name: "search"},
            filterDropdown: [
                    {name: "owner_id", class: "w200", options: <?php echo json_encode($owners_dropdown); ?>},
                {name: "label_id", class: "w200", options: <?php echo $labels_dropdown; ?>},
                {name: "source", class: "w200", options: <?php echo view("leads/lead_sources"); ?>},
<?php echo $custom_field_filters; ?>
            ],
            beforeRelaodCallback: function () {
                scrollLeft = $("#kanban-wrapper").scrollLeft();
            },
            afterRelaodCallback: function () {
                setTimeout(function () {
                    $("#kanban-wrapper").animate({scrollLeft: scrollLeft}, 'slow');
                }, 500);
            }
        });

    });

</script>
