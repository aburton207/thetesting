<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $survey->title; ?></title>
    <style>
        body {font-family: Arial, sans-serif; padding: 20px; background: #f7f7f7; color: #333;}
        .nps-form {max-width: 420px; margin: auto; text-align: center; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        .nps-question {margin-bottom: 20px;}
        .nps-scale {display: flex; justify-content: space-between; margin-top: 10px;}
        .nps-option {display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; transition: background 0.2s, color 0.2s;}
        .nps-option input {display: none;}
        .nps-option span {display: block; width: 100%; line-height: 32px;}
        .nps-option input:checked + span {background: #007bff; color: #fff; border-color: #007bff;}
        .nps-submit-button {margin-top: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;}
        .nps-submit-button:hover {background: #0056b3;}
    </style>
</head>
<body>
    <?php echo view('Polls\\Views\\nps\\form', ['survey' => $survey, 'questions' => $questions, 'show_submit' => true]); ?>
    <script src="<?php echo base_url('assets/js/nps_embed.js'); ?>"></script>
</body>
</html>
