<div class="card border-top-0 rounded-top-0 mb0 mt10 no-box-shadow">
    <div class="card-header title-tab clearfix">
        <h4 class="float-start"><?php echo app_lang('clients'); ?></h4>
   
    </div>
    <div class="bg-white">
        <div id="kanban-filters"></div>
    </div>
</div>

<div id="load-kanban"></div>

<script>
    $(document).ready(function() {
        window.scrollToKanbanContent = true;
    });
</script>

<?php echo view("clients/kanban/all_clients_kanban_helper_js"); ?>