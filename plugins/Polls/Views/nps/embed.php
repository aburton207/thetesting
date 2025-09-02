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
    <?php echo view('Polls\\Views\\nps\\form', ['survey' => $survey, 'questions' => $questions, 'show_submit' => false]); ?>
    <script src="<?php echo base_url('assets/js/nps_embed.js'); ?>"></script>
</body>
</html>
