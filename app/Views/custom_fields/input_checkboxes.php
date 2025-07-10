<div class="checkbox-group">
    <?php
    $uid = "_" . uniqid(rand());

    $options = $field_info->options;
    $options_array = explode(",", $options);

    $saved_values = isset($field_info->value) ? array_map('trim', explode(",", $field_info->value)) : [];

    if ($options && count($options_array)) {
        foreach ($options_array as $value) {
            $value = trim($value);
            $isChecked = in_array($value, $saved_values);
            // Sanitize value for CSS class: lowercase, replace spaces/special chars with hyphens
            $sanitized_class = 'option-' . preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($value)));
            ?>
            <div class="<?php echo $sanitized_class; ?>" id="custom_<?php echo htmlspecialchars($value); ?>"><i class="fa-duotone"></i>
                <?php
                echo form_checkbox(array(
                    "id" => $value . $field_info->id,
                    "name" => "custom_field_" . $field_info->id,
                    "class" => "form-check-input validate-hidden",
                    "data-rule-required" => $field_info->required ? "true" : "false",
                    "data-msg-required" => app_lang("field_required"),
                    "data-prepare_checkboxes_data" => "1",
                ), $value, $isChecked);
                ?>
                <label for="<?php echo htmlspecialchars($value . $uid); ?>" class="mr15"><?php echo htmlspecialchars($value); ?></label>
            </div>
            <?php
        }
    }
    ?>
</div>