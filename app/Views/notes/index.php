<div class="card border-top-0 rounded-top-0">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('notes'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("notes/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_note'), array("class" => "btn btn-default", "title" => app_lang('add_note'), "data-post-client_id" => $client_id)); ?>           
        </div>
    </div>
    <div class="table-responsive">
        <table id="note-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#note-table").appTable({
            source: '<?php echo_uri("notes/list_data/client/" . $client_id) ?>',
            order: [[0, 'desc']],
            columns: [
                {targets: [0], visible: false}, // Hidden raw created_at
                {title: '<?php echo app_lang("created_date"); ?>', "class": "w200"}, // Relative time
                {title: '<?php echo app_lang("title"); ?>', "class": "all"}, // Title
                {title: '<?php echo app_lang("author") ?>', "class": "w150"}, // Author
                {title: '<?php echo app_lang("description") ?>', "class": "w500"}, // Description (widened)
                {title: '<?php echo app_lang("files") ?>', "class": "w250"}, // Files
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"} // Actions
            ]
        });
    });
</script>