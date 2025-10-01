<form action="<?php echo get_uri("external_tickets/save"); ?>" role="form" method="post" accept-charset="utf-8" id="external-ticket-form">

    <input type="hidden" name="is_embedded_form" value="1" />
    <input type="hidden" name="redirect_to" value="https://www.avenirenergy.ca/avenir-energy-thank-you" />

    <?php if (!empty($selected_assignee_id)) { ?>
        <input type="hidden" name="assigned_to" value="<?php echo htmlspecialchars($selected_assignee_id); ?>" />
    <?php } ?>

    <?php if (!empty($selected_label_ids)) { ?>
        <input type="hidden" name="labels" value="<?php echo htmlspecialchars($selected_label_ids); ?>" />
    <?php } ?>

    <input type="text" name="title" id="title" placeholder="<?php echo app_lang('title'); ?>" required="required" />

    <?php if (!empty($selected_ticket_type_id)) { ?>
        <input type="hidden" name="ticket_type_id" value="<?php echo htmlspecialchars($selected_ticket_type_id); ?>" />
    <?php } else { ?>
        <select name="ticket_type_id" id="ticket_type_id">
            <option value=""><?php echo "- " . app_lang('ticket_type') . " -"; ?></option>
            <?php if (!empty($ticket_types)) { foreach ($ticket_types as $type) { ?>
                <option value="<?php echo htmlspecialchars($type->id); ?>"><?php echo htmlspecialchars($type->title); ?></option>
            <?php }} ?>
        </select>
    <?php } ?>

    <input type="email" name="email" id="email" placeholder="<?php echo app_lang('your_email'); ?>" autocomplete="off" required="required" />

    <input type="text" name="name" id="name" placeholder="<?php echo app_lang('your_name'); ?>" />

    <?php if (!empty($custom_fields)) {
        foreach ($custom_fields as $field) {
            $field_title = isset($field->title_language_key) && $field->title_language_key ? app_lang($field->title_language_key) : $field->title;
            $placeholder_language_key = isset($field->placeholder_language_key) ? $field->placeholder_language_key : "";
            $placeholder_value = isset($field->placeholder) ? $field->placeholder : "";
            $placeholder = $placeholder_language_key ? app_lang($placeholder_language_key) : ($placeholder_value ? $placeholder_value : $field_title);
            ?>
            <input type="text" name="custom_field_<?php echo $field->id; ?>" id="custom_field_<?php echo $field->id; ?>" placeholder="<?php echo htmlspecialchars($placeholder); ?>" />
        <?php }
    } ?>

    <textarea name="description" id="description" placeholder="<?php echo app_lang('description'); ?>" required="required"></textarea>

    <button type="submit"><?php echo app_lang('submit'); ?></button>

</form>
