<form action="<?php echo get_uri("collect_leads/save"); ?>" role="form" method="post" accept-charset="utf-8" id="lead-form">

    <?php if (!empty($lead_source_id)) { ?>
        <input type="hidden" name="lead_source_id" value="<?php echo $lead_source_id; ?>" />
    <?php } ?>
    <?php if (!empty($lead_owner_id)) { ?>
        <input type="hidden" name="lead_owner_id" value="<?php echo $lead_owner_id; ?>" />
    <?php } ?>
    <?php if (!empty($lead_labels)) { ?>
        <input type="hidden" name="lead_labels" value="<?php echo $lead_labels; ?>" />
    <?php } ?>

    <input type="text" name="company_name" id="company_name" placeholder="<?php echo app_lang('company_name'); ?>" />
    <input type="text" name="first_name" id="first_name" placeholder="<?php echo app_lang('first_name'); ?>" />
    <input type="text" name="last_name" id="last_name" placeholder="<?php echo app_lang('last_name'); ?>" required="required" />
    <input type="email" name="email" id="email" placeholder="<?php echo app_lang('email'); ?>" autocomplete="off" />
    <input type="text" name="address" id="address" placeholder="<?php echo app_lang('address'); ?>" />
    <input type="text" name="city" id="city" placeholder="<?php echo app_lang('city'); ?>" />
    <input type="text" name="state" id="state" placeholder="<?php echo app_lang('state'); ?>" />
    <input type="text" name="zip" id="zip" placeholder="<?php echo app_lang('zip'); ?>" />
    <input type="text" name="country" id="country" placeholder="<?php echo app_lang('country'); ?>" />
    <input type="text" name="phone" id="phone" placeholder="<?php echo app_lang('phone'); ?>" />

    <?php if (!empty($custom_fields)) {
        foreach ($custom_fields as $field) { ?>
            <input type="text" name="custom_field_<?php echo $field->id; ?>" placeholder="<?php echo $field->title; ?>" />
    <?php }
    } ?>

    <button type="submit"><?php echo app_lang('submit'); ?></button>

</form>

<script>
document.getElementById('lead-form').addEventListener('submit', function() {
    var company = document.getElementById('company_name');
    if (company && !company.value.trim()) {
        var first = document.getElementById('first_name').value.trim();
        var last = document.getElementById('last_name').value.trim();
        company.value = (first + ' ' + last).trim();
    }
});
</script>
