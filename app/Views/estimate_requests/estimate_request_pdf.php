<?php
// app/Views/estimate_requests/estimate_request_pdf.php
if (!isset($estimate_request_info) || !is_object($estimate_request_info) || !isset($client_info) || !is_object($client_info)) {
    log_message('error', 'Invalid data provided to estimate_request_pdf view: estimate_request_info=' . print_r($estimate_request_info ?? 'null', true) . ', client_info=' . print_r($client_info ?? 'null', true));
    throw new \Exception('Invalid data provided to estimate_request_pdf view');
}

$color = get_setting("invoice_color") ?: "#2AA384";
$style = get_setting("invoice_style") ?: "style_1";
$item_background = get_setting("invoice_item_list_background") ?: "#f5f5f5";

$header_data = [
    "client_info" => $client_info,
    "color" => $color,
    "estimate_request_info" => $estimate_request_info
];

log_message('debug', 'Header data for PDF: ' . print_r($header_data, true));

// Load Custom_field_values_model for CodeIgniter 4
$custom_field_model = new \App\Models\Custom_field_values_model();
$custom_field_options = ["related_to_type" => "estimate_request", "related_to_id" => $estimate_request_info->id];
$custom_fields = $custom_field_model->get_details($custom_field_options)->getResult();
?>

<div style="margin: auto;">
    <?php echo view('estimate_requests/estimate_request_header', $header_data); ?>
</div>

<br />

<table class="table-responsive" style="width: 100%;">
    <tr style="font-weight: bold; background-color: <?php echo $color; ?>; color: #fff;">
        <th style="width: 45%; border-right: 1px solid #eee;"> <?php echo app_lang("field"); ?> </th>
        <th style="text-align: left; width: 55%; border-right: 1px solid #eee;"> <?php echo app_lang("value"); ?> </th>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("form_title"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo $estimate_request_info->form_title; ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("client"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo $client_info->company_name; ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("address"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo nl2br($client_info->address ?? ''); ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("city"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo $client_info->city ?? ''; ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("state"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo $client_info->state ?? ''; ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("zip"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo $client_info->zip ?? ''; ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("country"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo $client_info->country ?? ''; ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("phone"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo $client_info->phone ?? ''; ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("assigned_to"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo $estimate_request_info->assigned_to_user; ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("status"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo ucfirst($estimate_request_info->status); ?></td>
    </tr>
    <tr style="background-color: <?php echo $item_background; ?>;">
        <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo app_lang("created_at"); ?></td>
        <td style="text-align: left; width: 55%; border: 1px solid #fff;"><?php echo format_to_date($estimate_request_info->created_at, false); ?></td>
    </tr>
    <?php foreach ($custom_fields as $field) { ?>
        <tr style="background-color: <?php echo $item_background; ?>;">
            <td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo $field->custom_field_title; ?></td>
            <td style="text-align: left; width: 55%; border: 1px solid #fff;">
                <?php echo view("custom_fields/output_" . $field->custom_field_type, ["value" => $field->value]); ?>
            </td>
        </tr>
    <?php } ?>
</table>

<br /><br />
<table class="invoice-pdf-hidden-table" style="border-top: 1px solid #f2f4f6; margin: 0; padding: 0; display: block; width: 100%; height: 10px;"></table>

<span style="color:#444; line-height: 14px;">
    <?php echo get_setting("invoice_footer"); ?>
</span>