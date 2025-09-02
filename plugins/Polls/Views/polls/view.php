<?php if ($view_type != "modal_view") { ?>

    <div id="page-content" class="page-wrapper pb0 clearfix poll-preview" style="max-width: 1000px; min-width: 400px; margin: auto;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="page-title clearfix">
                        <h1><?php echo app_lang("poll") . " #$model_info->id"; ?></h1>

                        <?php if ($model_info->created_by == $login_user->id || $login_user->is_admin || $can_create_polls) { ?>
                            <div class="title-button-group">
                                <span class="dropdown inline-block">
                                    <button class="btn btn-default dropdown-toggle caret mr0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i data-feather='settings' class='icon-16'></i> <?php echo app_lang('actions'); ?>
                                    </button>
                                    <ul class="dropdown-menu float-end" role="menu">
                                        <li role="presentation"><?php echo modal_anchor(get_uri("polls/modal_form"), "<i data-feather='edit-2' class='icon-16'></i> " . app_lang('polls_edit_poll'), array("title" => app_lang('polls_edit_poll'), "data-post-id" => $model_info->id, "id" => "poll-details-edit-btn", "class" => "dropdown-item")); ?></li>
                                        <?php if ($model_info->status == "inactive") { ?>
                                            <li role="presentation"><?php echo ajax_anchor(get_uri("polls/save_poll_status/$model_info->id/active"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('polls_mark_as_active'), array("class" => "dropdown-item", "title" => app_lang('polls_mark_as_active'), "data-reload-on-success" => "1")); ?></li>
                                        <?php } else { ?>
                                            <li role="presentation"><?php echo ajax_anchor(get_uri("polls/save_poll_status/$model_info->id/inactive"), "<i data-feather='x-circle' class='icon-16'></i> " . app_lang('polls_mark_as_inactive'), array("class" => "dropdown-item", "title" => app_lang('polls_mark_as_inactive'), "data-reload-on-success" => "1")); ?></li>
                                        <?php } ?>
                                    </ul>
                                </span>
                            </div> 
                        <?php } ?>

                    </div>

                    <div class="card-body">
                        <?php echo view("Polls\Views\polls\\poll_view_data"); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

<?php } else { ?>

    <div class="modal-body clearfix">
        <div class="card">
            <?php echo view("Polls\Views\polls\\poll_view_data"); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

        <?php
        if ($model_info->created_by == $login_user->id || $login_user->is_admin || $can_create_polls) {
            echo modal_anchor(get_uri("polls/modal_form"), "<i data-feather='edit-2' class='icon-16'></i> " . app_lang('polls_edit_poll'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "title" => app_lang('polls_edit_poll')));

            if ($model_info->status == "inactive") {
                echo ajax_anchor(get_uri("polls/save_poll_status/$model_info->id/active"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('polls_mark_as_active'), array("class" => "btn btn-success", "title" => app_lang('polls_mark_as_active'), "data-reload-on-success" => "1"));
            } else {
                echo ajax_anchor(get_uri("polls/save_poll_status/$model_info->id/inactive"), "<i data-feather='x-circle' class='icon-16'></i> " . app_lang('polls_mark_as_inactive'), array("class" => "btn btn-danger", "title" => app_lang('polls_mark_as_inactive'), "data-reload-on-success" => "1"));
            }
        }
        ?>
    </div>

<?php } ?>

<?php
$poll_link = anchor(get_uri("polls/view/$model_info->id"), '<i data-feather="external-link" class="icon-16 task-link-btn"></i>', array("target" => "_blank", "class" => "p15"));
?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#save_vote_form").appForm({
            isModal: false,
            onSuccess: function (response) {
                console.log(response);
            }
        });

        $("#show-pull-result-checkbox").on('click', function () {
            if ($(this).prop("checked")) {
<?php foreach ($poll_answers as $poll_answer) { ?>
                    var pollAnsId = <?php echo $poll_answer->id; ?>;
                    var percentage = $(".vote-" + pollAnsId).data("percentage");
                    $(".vote-" + pollAnsId).css("width", percentage + "%");
<?php } ?>
                $(".vote-result-section").removeClass("hide");
            } else {
                $(".progress-bar").css("width", "0%");
                $(".vote-result-section").addClass("hide");
            }
        });

        $(".poll_answer_id").on('change', function () {
            var getPollAnsId = $('input[name="poll_answer_id"]:checked').val();
            $(".vote-button").attr("data-post-poll_answer_id", getPollAnsId);
            var actionUrl = "<?php echo get_uri('polls/save_vote/' . $model_info->id . '/details/'); ?>" + getPollAnsId;
            $(".vote-button-details").attr("data-action-url", actionUrl);
        });


        $('.vote-button').addClass("disabled");
        $('.vote-button-details').addClass("disabled");

        $('.poll_answer').on('change', function () {
            $('.vote-button').removeClass("disabled");
            $('.vote-button-details').removeClass("disabled");
        });

        //for reloading the view
        $("body").on("click", "#poll-details-edit-btn", function () {
            if ($(".poll-preview").length) {
                //remove poll details view when it's already opened to prevent selector duplication
                $("#page-content").remove();
                $('#ajaxModal').on('hidden.bs.modal', function () {
                    location.reload();
                });
            }
        });

        //add a clickable link in poll title.
        $("#ajaxModalTitle").append('<?php echo $poll_link; ?>');

    });
</script>