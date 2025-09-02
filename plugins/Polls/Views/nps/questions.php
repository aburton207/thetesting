<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo $survey->title; ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("nps/question_form"), "<i data-feather='plus' class='icon-16'></i>" . app_lang('add'), array("class" => "btn btn-default", "title" => app_lang('add'), "data-post-survey_id" => $survey->id)); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="display dataTable" width="100%">
                <thead>
                    <tr>
                        <th><?php echo app_lang("question"); ?></th>
                        <th class="text-center option w100"><i data-feather='menu' class='icon-16'></i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $question) { ?>
                        <tr>
                            <td><?php echo $question->question_text; ?></td>
                            <td class="text-center option">
                                <?php
                                echo modal_anchor(get_uri("nps/question_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit'), "data-post-id" => $question->id, "data-post-survey_id" => $survey->id));
                                echo js_anchor("<i data-feather='x' class='icon-16'></i>", array("title" => app_lang('delete'), "class" => "delete", "data-id" => $question->id, "data-action-url" => get_uri("nps/delete_question"), "data-action" => "delete-confirmation"));
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
