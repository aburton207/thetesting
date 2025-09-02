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
<?php if(isset($show_submit) && $show_submit){ ?>
    <button type="submit" class="btn btn-primary"><?php echo app_lang('submit'); ?></button>
<?php } ?>
<?php echo form_close(); ?>
