<?php echo form_open(get_uri("nps/submit"), ["id" => "nps-form", "class" => "nps-form"]); ?>
<input type="hidden" name="survey_id" value="<?php echo $survey->id; ?>" />
<?php foreach ($questions as $question) { ?>
    <div class="nps-question">
        <label class="nps-question-text"><?php echo $question->question_text; ?></label>
        <div class="nps-scale">
            <?php for ($i = 0; $i <= 10; $i++) { ?>
                <label class="nps-option">
                    <input type="radio" name="score[<?php echo $question->id; ?>]" value="<?php echo $i; ?>" required>
                    <span><?php echo $i; ?></span>
                </label>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<?php if (isset($show_submit) && $show_submit) { ?>
    <button type="submit" class="btn btn-primary nps-submit-button"><?php echo app_lang('submit'); ?></button>
<?php } ?>
<?php echo form_close(); ?>

