<div id="page-content" class="page-wrapper pb0 clearfix grid-button clients-kanban-view">

    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab clients-kanban">
            <h4 class="pl15 pt10 pr15"><?php echo app_lang('clients'); ?></h4>
        </li>

        <li><a role="presentation" href="<?php echo get_uri('clients'); ?>"><?php echo app_lang('overview'); ?></a></li>
        <li><a role="presentation" href="<?php echo get_uri('clients/clients_list'); ?>"><?php echo app_lang('clients'); ?></a></li>
        <li class="active"><a role="presentation" href="<?php echo get_uri('clients/all_clients_kanban'); ?>"><?php echo app_lang('kanban'); ?></a></li>
        <li><a role="presentation" href="<?php echo get_uri('clients/contacts'); ?>"><?php echo app_lang('contacts'); ?></a></li>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php
                if (isset($can_edit_clients) && $can_edit_clients) {
                    echo modal_anchor(get_uri('labels/modal_form'), "<i data-feather='tag' class='icon-16'></i> " . app_lang('manage_labels'), array("class" => "btn btn-default", "title" => app_lang('manage_labels'), "data-post-type" => "client"));
                    echo modal_anchor(get_uri('clients/import_modal_form'), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_clients'), array("class" => "btn btn-default", "title" => app_lang('import_clients'), "id" => 'import-btn'));
                    echo modal_anchor(get_uri('clients/modal_form'), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_client'), array("class" => "btn btn-default", "title" => app_lang('add_client')));
                }
                ?>
            </div>
        </div>
    </ul>

    <div class="card border-top-0 rounded-top-0 mb0">
        <div class="bg-white" id="js-kanban-filter-container">
            <div id="kanban-filters"></div>
        </div>

        <div id="load-kanban"></div>
    </div>
</div>

<script>
    $(document).ready(function () {
        window.scrollToKanbanContent = true;
    });
</script>

<?php echo view("clients/kanban/all_clients_kanban_helper_js"); ?>