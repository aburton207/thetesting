<link rel="stylesheet" href="<?php echo base_url('plugins/Polls/assets/css/nps_form.css'); ?>" />
<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <h3><?php echo $survey->title; ?></h3>
            <p><?php echo $survey->description; ?></p>

            <?php echo view('Polls\\Views\\nps\\form', ['survey' => $survey, 'questions' => $questions, 'show_submit' => true]); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#nps-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                var messageHtml = "<div id='nps-response-message' class='nps-response-message'>" + result.message + "</div>";
                $("#nps-form").replaceWith(messageHtml);
            }
        });
    });
</script>
