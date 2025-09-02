<?php if ($embedded) { ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $survey->title; ?></title>
    <style>
        body {font-family: Arial, sans-serif; padding: 15px;}
        .nps-scale label {margin-right: 6px;}
        .nps-question {margin-bottom: 15px;}
    </style>
</head>
<body>
<?php } else { ?>
<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
<?php } ?>

<h3><?php echo $survey->title; ?></h3>
<p><?php echo $survey->description; ?></p>

<?php echo form_open(get_uri("nps/submit"), array("id" => "nps-form")); ?>
<input type="hidden" name="survey_id" value="<?php echo $survey->id; ?>" />
<?php foreach ($questions as $question) { ?>
    <div class="nps-question">
        <label><?php echo $question->title; ?></label>
        <div class="nps-scale">
            <?php for ($i = 0; $i <= 10; $i++) { ?>
                <label><input type="radio" name="score[<?php echo $question->id; ?>]" value="<?php echo $i; ?>" required> <?php echo $i; ?></label>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<button type="submit" class="btn btn-primary"><?php echo app_lang('submit'); ?></button>
<?php echo form_close(); ?>

<?php if ($embedded) { ?>
<script>
var form = document.getElementById('nps-form');
form.addEventListener('submit', function (e) {
    e.preventDefault();
    fetch(form.action, {method: 'POST', body: new FormData(form)})
        .then(function (response) { return response.json(); })
        .then(function (data) {
            form.innerHTML = data.message;
        });
});
</script>
</body>
</html>
<?php } else { ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#nps-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                $("#nps-form").html(result.message);
            }
        });
    });
</script>
        </div>
    </div>
</div>
<?php } ?>
