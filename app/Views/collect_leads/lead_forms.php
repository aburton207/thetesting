<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('lead_forms'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri('lead_forms/modal_form'), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_form'), array("class" => "btn btn-default", "title" => app_lang('add_form'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="lead-forms-table" class="display" cellspacing="0" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#lead-forms-table").appTable({
            source: '<?php echo_uri("lead_forms/list_data") ?>',
            order: [[0, 'asc']],
            columns: [
                {title: "<?php echo app_lang('title'); ?>", "class": "all"},
                {title: "<?php echo app_lang('owner'); ?>", "class": "w200"},
                {title: "<?php echo app_lang('region'); ?>", "class": "w150"},
                {title: "<?php echo app_lang('labels'); ?>", "class": "w200"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>
