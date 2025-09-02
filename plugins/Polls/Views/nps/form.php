<?php echo form_open(get_uri("nps/submit"), ["id" => "nps-form", "class" => "nps-form"]); ?>
  <div class="nps-logo">
    <img src="<?php echo get_logo_url(); ?>" alt="Logo">
  </div>

  <p class="nps-pretext">How likely are you to recommend us to a friend or colleague?</p>

  <input type="hidden" name="survey_id" value="<?php echo $survey->id; ?>" />

  <?php foreach ($questions as $question) { ?>
    <fieldset class="nps-question">
      <legend class="nps-question-text"><?php echo $question->question_text; ?></legend>

      <div class="nps-scale" role="radiogroup" aria-label="NPS scale 0 to 10">
        <?php for ($i = 0; $i <= 10; $i++) { ?>
          <label class="nps-option nps-<?php echo $i; ?>">
            <input
              type="radio"
              name="score[<?php echo $question->id; ?>]"
              value="<?php echo $i; ?>"
              required
              aria-label="<?php echo $i; ?>"
            >
            <span><?php echo $i; ?></span>
          </label>
        <?php } ?>
      </div>

      <div class="nps-scale-labels" aria-hidden="true">
        <span class="nps-label-left">&#128577; 0 – Not likely</span>
        <span class="nps-label-right">10 – Very likely &#128515;</span>
      </div>

      <textarea
        name="comment[<?php echo $question->id; ?>]"
        class="nps-comment"
        placeholder="<?php echo app_lang('additional_comments'); ?>"
      ></textarea>
    </fieldset>
  <?php } ?>

  <?php if (isset($show_submit) && $show_submit) { ?>
    <button type="submit" class="nps-submit-button"><?php echo app_lang('submit'); ?></button>
  <?php } ?>
<?php echo form_close(); ?>
