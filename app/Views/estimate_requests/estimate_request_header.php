<?php
// app/Views/estimate_requests/estimate_request_header.php
if (!isset($client_info) || !is_object($client_info) || !isset($estimate_request_info) || !is_object($estimate_request_info)) {
    log_message('error', 'Invalid data provided to estimate_request_header: client_info=' . print_r($client_info ?? 'null', true) . ', estimate_request_info=' . print_r($estimate_request_info ?? 'null', true));
    throw new \Exception('Invalid data provided to estimate_request_header');
}

$color = isset($color) ? $color : '#2AA384';
$logo_url = 'https://avenirenergy.work/files/system/company_1_file6749b6c30c63e-avenir-sm-logo.png';
?>

<table class="header-style" style="font-size: 13.5px; width: 100%;">
    <tr class="estimate-request-header-row">
        <td style="width: 50%; vertical-align: top;">
            <img src="<?php echo $logo_url; ?>" alt="Company Logo" style="max-height: 50px;" />
        </td>
        <td style="width: 50%; vertical-align: top; text-align: right; color: <?php echo $color; ?>;">
            <h3><?php echo app_lang('estimate_request') . ' #' . $estimate_request_info->id; ?></h3>
            <p><?php echo app_lang('date') . ': ' . format_to_date($estimate_request_info->created_at, false); ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding: 5px;"></td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top; text-align: left;">
            <strong><?php echo app_lang('client'); ?>:</strong> <?php echo $client_info->company_name ?? 'Unknown Client'; ?><br />
            <?php if ($estimate_request_info->status) { ?>
                <strong><?php echo app_lang('status'); ?>:</strong> <?php echo ucfirst($estimate_request_info->status); ?>
            <?php } ?>
        </td>
    </tr>
</table>

<?php
// Log for debugging
log_message('debug', 'Estimate request header logo: ' . $logo_url);
?>