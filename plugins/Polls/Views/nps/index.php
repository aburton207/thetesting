<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('nps'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("nps/modal_form"), "<i data-feather='plus' class='icon-16'></i>" . app_lang('add'), array("class" => "btn btn-default", "title" => app_lang('add'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="display dataTable" width="100%">
                <thead>
                    <tr>
                        <th><?php echo app_lang("title"); ?></th>
                        <th><?php echo app_lang("status"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($surveys as $survey) { ?>
                        <tr>
                            <td><?php echo anchor(get_uri("nps/report/" . $survey->id), $survey->title); ?></td>
                            <td><?php echo $survey->status; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
