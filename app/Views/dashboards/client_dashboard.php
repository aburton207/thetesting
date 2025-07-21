<?php echo view("dashboards/install_pwa"); ?>

<div id="page-content" class="page-wrapper clearfix">
    <?php
    echo announcements_alert_widget();

    app_hooks()->do_action('app_hook_dashboard_announcement_extension');
    ?>

    <?php if ($show_project_info) { ?>
        <div class="">
            <?php echo view("clients/projects/index"); ?>
        </div>
    <?php } ?>

</div>