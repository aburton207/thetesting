<?php
$default_lead_status_id = isset($default_lead_status_id) ? $default_lead_status_id : "";
$owner_id = isset($owner_id) ? $owner_id : (isset($login_user->id) ? $login_user->id : "");
$lead_source_id = isset($lead_source_id) ? $lead_source_id : "";
$ticket_id = isset($ticket_id) ? $ticket_id : "";
$default_country = isset($default_country) ? $default_country : "";
$can_edit_owner = isset($can_edit_owner) ? $can_edit_owner : false;
$owners_dropdown = isset($owners_dropdown) ? $owners_dropdown : array();
$sources_dropdown = isset($sources_dropdown) ? $sources_dropdown : array();
?>

<?php echo form_open(get_uri("clients/save_mobile"), array("id" => "mobile-client-form", "class" => "general-form", "role" => "form")); ?>
<?php echo form_hidden("lead_status_id", $default_lead_status_id); ?>
<?php if ($ticket_id) { ?>
    <?php echo form_hidden("ticket_id", $ticket_id); ?>
<?php } ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="mobile-client-form-wrapper">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mobile-client-form-title mb-0"><?php echo app_lang('add_client'); ?></h3>
            <a href="<?php echo get_uri('clients'); ?>" class="btn btn-link p0 mobile-client-back-link">
                <i data-feather="arrow-left" class="icon-16"></i> <?php echo app_lang('back'); ?>
            </a>
        </div>

        <div class="mobile-client-card">
            <div class="mobile-form-section">
                <div class="mb-3">
                    <label for="first_name" class="form-label"><?php echo app_lang('first_name'); ?><span class="text-danger">*</span></label>
                    <?php
                    echo form_input(array(
                        "id" => "first_name",
                        "name" => "first_name",
                        "class" => "form-control",
                        "placeholder" => app_lang('first_name'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang('field_required')
                    ));
                    ?>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label"><?php echo app_lang('last_name'); ?><span class="text-danger">*</span></label>
                    <?php
                    echo form_input(array(
                        "id" => "last_name",
                        "name" => "last_name",
                        "class" => "form-control",
                        "placeholder" => app_lang('last_name'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang('field_required')
                    ));
                    ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label"><?php echo app_lang('email'); ?><span class="text-danger">*</span></label>
                    <?php
                    echo form_input(array(
                        "id" => "email",
                        "name" => "email",
                        "class" => "form-control",
                        "placeholder" => app_lang('email'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang('field_required'),
                        "data-rule-email" => true,
                        "data-msg-email" => app_lang('enter_valid_email')
                    ));
                    ?>
                </div>
                <div class="mb-3">
                    <label for="company_name" class="form-label"><?php echo app_lang('company_name'); ?></label>
                    <?php
                    echo form_input(array(
                        "id" => "company_name",
                        "name" => "company_name",
                        "class" => "form-control",
                        "placeholder" => app_lang('company_name')
                    ));
                    ?>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label"><?php echo app_lang('phone'); ?></label>
                    <?php
                    echo form_input(array(
                        "id" => "phone",
                        "name" => "phone",
                        "class" => "form-control",
                        "placeholder" => app_lang('phone')
                    ));
                    ?>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label"><?php echo app_lang('address'); ?></label>
                    <?php
                    echo form_input(array(
                        "id" => "address",
                        "name" => "address",
                        "class" => "form-control",
                        "placeholder" => app_lang('address')
                    ));
                    ?>
                </div>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="city" class="form-label"><?php echo app_lang('city'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "city",
                                "name" => "city",
                                "class" => "form-control",
                                "placeholder" => app_lang('city')
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="state" class="form-label"><?php echo app_lang('state'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "state",
                                "name" => "state",
                                "class" => "form-control",
                                "placeholder" => app_lang('state')
                            ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="zip" class="form-label"><?php echo app_lang('zip'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "zip",
                                "name" => "zip",
                                "class" => "form-control",
                                "placeholder" => app_lang('zip')
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="country" class="form-label"><?php echo app_lang('country'); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "country",
                                "name" => "country",
                                "class" => "form-control",
                                "placeholder" => app_lang('country'),
                                "value" => $default_country
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="mobile_lead_source_id" class="form-label"><?php echo app_lang('source'); ?></label>
                    <?php echo view('partials/lead_source_select', array('sources_dropdown' => $sources_dropdown, 'selected' => $lead_source_id, 'id' => 'mobile_lead_source_id')); ?>
                </div>

                <?php if ($can_edit_owner && !empty($owners_dropdown)) { ?>
                    <div class="mb-3">
                        <label for="mobile_owner_id" class="form-label"><?php echo app_lang('owner'); ?></label>
                        <?php echo form_dropdown("owner_id", $owners_dropdown, $owner_id, "class='form-control' id='mobile_owner_id'"); ?>
                    </div>
                <?php } else { ?>
                    <?php echo form_hidden("owner_id", $owner_id); ?>
                <?php } ?>
            </div>
        </div>

        <div class="mobile-client-form-actions">
            <button type="submit" class="btn btn-primary btn-lg w-100">
                <span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?>
            </button>
        </div>
    </div>
</div>

<?php echo form_close(); ?>

<style>
    .mobile-client-form-wrapper {
        max-width: 640px;
        margin: 0 auto;
        padding: 20px 15px 40px;
    }

    .mobile-client-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 12px 30px rgba(23, 43, 77, 0.1);
    }

    .mobile-client-form-wrapper label.form-label {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .mobile-client-card .form-control,
    .mobile-client-card select.form-control {
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 1rem;
    }

    .mobile-client-form-actions {
        margin-top: 24px;
    }

    .mobile-client-form-actions .btn {
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 1.05rem;
        font-weight: 600;
    }

    .mobile-client-back-link {
        font-weight: 600;
        color: #234b7c;
    }

    @media (min-width: 768px) {
        .mobile-client-form-wrapper {
            padding-top: 40px;
        }
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        var $form = $("#mobile-client-form");

        $form.appForm({
            isModal: false,
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                if (result && result.success) {
                    appAlert.success(result.message, {duration: 10000});
                    if (result.redirect_url) {
                        setTimeout(function () {
                            window.location.href = result.redirect_url;
                        }, 800);
                    }
                }
            }
        });

        setTimeout(function () {
            $("#first_name").trigger("focus");
        }, 200);
    });
</script>
