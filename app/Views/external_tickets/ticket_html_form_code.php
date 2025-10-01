<form action="<?php echo get_uri("external_tickets/save"); ?>" role="form" method="post" accept-charset="utf-8" id="external-ticket-form">

    <input type="hidden" name="is_embedded_form" value="1" />
    <input type="hidden" name="redirect_to" value="https://www.avenirenergy.ca/avenir-energy-thank-you" />

    <?php
    $default_assignee_id = !empty($selected_assignee_id) ? htmlspecialchars($selected_assignee_id) : "";
    ?>
    <input type="hidden" name="assigned_to" id="assigned_to" value="<?php echo $default_assignee_id; ?>" data-default-value="<?php echo $default_assignee_id; ?>" />

    <?php if (!empty($selected_label_ids)) { ?>
        <input type="hidden" name="labels" value="<?php echo htmlspecialchars($selected_label_ids); ?>" />
    <?php } ?>

    <input type="text" name="first_name" id="first_name" placeholder="<?php echo app_lang('first_name'); ?>" required="required" />

    <input type="text" name="last_name" id="last_name" placeholder="<?php echo app_lang('last_name'); ?>" required="required" />

    <input type="text" name="company_name" id="company_name" placeholder="<?php echo app_lang('company_name'); ?>" />

    <input type="email" name="email" id="email" placeholder="<?php echo app_lang('email'); ?>" autocomplete="off" required="required" />

    <input type="text" name="address" id="address" placeholder="<?php echo app_lang('address'); ?>" autocomplete="off" required="required" />

    <input type="text" name="city" id="city" placeholder="<?php echo app_lang('city'); ?>" required="required" />

    <select name="state" id="state" required="required">
        <option value="">- Select Province -</option>
        <option value="New Brunswick">New Brunswick</option>
        <option value="Nova Scotia">Nova Scotia</option>
        <option value="Prince Edward Island">Prince Edward Island</option>
        <option value="Quebec">Quebec</option>
        <option value="Ontario">Ontario</option>
        <option value="Manitoba">Manitoba</option>
        <option value="Northwest Territories">Northwest Territories</option>
        <option value="British Columbia">British Columbia</option>
    </select>

    <input type="text" name="zip" id="zip" placeholder="<?php echo app_lang('zip'); ?>" required="required" />

    <select name="lead_source_id" id="lead_source_id" required="required">
        <option value="">- Select -</option>
        <option value="1">CA_Eastern ROC</option>
        <option value="2">CA_Pacific</option>
        <option value="3">CA_Prairies</option>
        <option value="4">CA_Atlantic</option>
        <option value="5">CA_Quebec</option>
        <option value="6">CA_Ontario</option>
    </select>

    <input type="tel" name="phone" id="phone" placeholder="<?php echo app_lang('phone'); ?>" minlength="10" required="required" />

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

<script type="text/javascript">
(function () {
    var stateField = document.getElementById('state');
    var cityField = document.getElementById('city');
    var leadSourceField = document.getElementById('lead_source_id');
    var assignedField = document.getElementById('assigned_to');

    var defaultAssignee = assignedField ? (assignedField.getAttribute('data-default-value') || assignedField.value || '') : '';

    var ownerMap = {
        '2': '254',
        '3': '253',
        '4': '251',
        '5': '3827',
        '6': '252'
    };

    var sourceMap = {
        'New Brunswick': '4',
        'Nova Scotia': '4',
        'Prince Edward Island': '4',
        'Quebec': '5',
        'Ontario': '6',
        'Manitoba': '3',
        'Northwest Territories': '3',
        'British Columbia': '3'
    };

    var bcCitiesKeywords = [
        'vancouver', 'burnaby', 'richmond', 'surrey', 'delta', 'new westminster',
        'langley', 'white rock', 'maple ridge', 'pitt meadows', 'coquitlam',
        'port coquitlam', 'port moody', 'north vancouver', 'west vancouver',
        'belcarra', 'anmore', 'bowen island', 'lions bay',
        'abbotsford', 'chilliwack', 'mission', 'kent', 'agassiz',
        'harrison hot springs', 'hope',
        'sechelt', 'gibsons',
        'victoria', 'saanich', 'oak bay', 'esquimalt', 'view royal', 'colwood',
        'langford', 'metchosin', 'highlands', 'sooke', 'central saanich', 'north saanich',
        'sidney', 'duncan', 'north cowichan', 'lake cowichan', 'ladysmith', 'nanaimo',
        'lantzville', 'parksville', 'qualicum beach', 'port alberni', 'tofino',
        'ucluelet', 'courtenay', 'comox', 'cumberland', 'campbell river', 'gold river',
        'tahsis', 'zeballos', 'port hardy', 'port mcneill', 'port alice', 'alert bay'
    ];

    function applyOwner(leadSourceId) {
        if (!assignedField) {
            return;
        }

        if (ownerMap.hasOwnProperty(leadSourceId) && ownerMap[leadSourceId]) {
            assignedField.value = ownerMap[leadSourceId];
        } else {
            assignedField.value = defaultAssignee;
        }
    }

    function updateLeadSourceFromLocation() {
        if (!leadSourceField || !stateField) {
            return;
        }

        var provinceVal = stateField.value;

        if (!provinceVal) {
            leadSourceField.value = '';
            applyOwner('');
            return;
        }

        var leadSource = sourceMap.hasOwnProperty(provinceVal) ? sourceMap[provinceVal] : '';

        if (provinceVal === 'British Columbia') {
            var cityVal = cityField ? cityField.value.trim().toLowerCase() : '';
            for (var i = 0; i < bcCitiesKeywords.length; i++) {
                if (cityVal.indexOf(bcCitiesKeywords[i]) !== -1) {
                    leadSource = '2';
                    break;
                }
            }
        }

        leadSourceField.value = leadSource;
        applyOwner(leadSource);
    }

    if (stateField) {
        stateField.addEventListener('change', updateLeadSourceFromLocation);
    }

    if (cityField) {
        cityField.addEventListener('change', updateLeadSourceFromLocation);
        cityField.addEventListener('keyup', updateLeadSourceFromLocation);
    }

    if (leadSourceField) {
        leadSourceField.addEventListener('change', function () {
            applyOwner(leadSourceField.value);
        });
    }

    updateLeadSourceFromLocation();
    if (leadSourceField) {
        applyOwner(leadSourceField.value);
    }
})();
</script>
