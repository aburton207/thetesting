<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $survey->title; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/nps_form.css'); ?>" />
</head>
<body>
    <?php echo view('Polls\\Views\\nps\\form', ['survey' => $survey, 'questions' => $questions, 'show_submit' => true]); ?>
    <script src="<?php echo base_url('assets/js/nps_embed.js'); ?>"></script>
</body>
</html>
