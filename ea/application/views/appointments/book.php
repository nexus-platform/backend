<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#35A768">

        <title><?= lang('page_title') . ' ' . $company_name ?></title>

        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/cookieconsent/cookieconsent.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/frontend.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">

        <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
        <link rel="icon" sizes="192x192" href="<?= asset_url('assets/img/logo.png') ?>">
        <script type="text/javascript">
            function iframe_resize() {
                var body = document.body,
                        html = document.documentElement,
                        height = Math.max(body.scrollHeight, body.offsetHeight,
                                html.clientHeight, html.scrollHeight, html.offsetHeight),
                        width = Math.max(body.scrollWidth, body.offsetWidth,
                                html.clientWidth, html.scrollWidth, html.offsetWidth)
                        ;
                if (window.parent.postMessage) {
                    window.parent.postMessage({height: height, width: width}, "*");
                }
            }
        </script>
    </head>

    <body onresize="iframe_resize();" onload="iframe_resize();">
        <div id="main" class="container">
            <div class="wrapper row">
                <div id="book-appointment-wizard" class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">

                    <!-- FRAME TOP BAR -->

                    <div id="header">
                        <span id="company-name"><?= $company_name ?></span>

                        <div id="steps">
                            <div id="step-1" class="book-step active-step" title="<?= lang('step_one_title') ?>">
                                <strong>1</strong>
                            </div>

                            <div id="step-2" class="book-step" title="<?= lang('step_two_title') ?>">
                                <strong>2</strong>
                            </div>
                            <div id="step-3" class="book-step" title="<?= lang('step_three_title') ?>">
                                <strong>3</strong>
                            </div>
                            <div id="step-4" class="book-step" title="<?= lang('step_four_title') ?>">
                                <strong>4</strong>
                            </div>
                        </div>
                    </div>

                    <?php if ($manage_mode): ?>
                        <div id="cancel-appointment-frame" class="booking-header-bar row">
                            <div class="col-xs-12 col-sm-10">
                                <p><?= lang('cancel_appointment_hint') ?></p>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <form id="cancel-appointment-form" method="post"
                                      action="<?= site_url('appointments/cancel/' . $appointment_data['hash']) ?>">
                                    <input type="hidden" name="csrfToken" value="<?= $this->security->get_csrf_hash() ?>" />
                                    <textarea name="cancel_reason" style="display:none"></textarea>
                                    <button id="cancel-appointment" class="btn btn-default btn-sm"><?= lang('cancel') ?></button>
                                </form>
                            </div>
                        </div>
                        <div class="booking-header-bar row">
                            <div class="col-xs-12 col-sm-10">
                                <p><?= lang('delete_personal_information_hint') ?></p>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <button id="delete-personal-information" class="btn btn-danger btn-sm"><?= lang('delete') ?></button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    if (isset($exceptions)) {
                        echo '<div style="margin: 10px">';
                        echo '<h4>' . lang('unexpected_issues') . '</h4>';
                        foreach ($exceptions as $exception) {
                            echo exceptionToHtml($exception);
                        }
                        echo '</div>';
                    }
                    ?>

                    <!-- SELECT SERVICE AND PROVIDER -->

                    <div id="wizard-frame-1" class="wizard-frame">
                        <div class="frame-container">
                            <h3 class="frame-title"><?= lang('step_one_title') ?></h3>

                            <div class="frame-content">
                                <div class="form-group">
                                    <label for="select-service">
                                        <strong><?= lang('select_service') ?></strong>
                                    </label>

                                    <select id="select-service" class="col-xs-12 col-sm-4 form-control">
                                        <?php
                                        // Group services by category, only if there is at least one service with a parent category.
                                        $has_category = FALSE;
                                        foreach ($available_services as $service) {
                                            if ($service['category_id'] != NULL) {
                                                $has_category = TRUE;
                                                break;
                                            }
                                        }

                                        if ($has_category) {
                                            $grouped_services = array();

                                            foreach ($available_services as $service) {
                                                if ($service['category_id'] != NULL) {
                                                    if (!isset($grouped_services[$service['category_name']])) {
                                                        $grouped_services[$service['category_name']] = array();
                                                    }

                                                    $grouped_services[$service['category_name']][] = $service;
                                                }
                                            }

                                            // We need the uncategorized services at the end of the list so
                                            // we will use another iteration only for the uncategorized services.
                                            $grouped_services['uncategorized'] = array();
                                            foreach ($available_services as $service) {
                                                if ($service['category_id'] == NULL) {
                                                    $grouped_services['uncategorized'][] = $service;
                                                }
                                            }

                                            foreach ($grouped_services as $key => $group) {
                                                $group_label = ($key != 'uncategorized') ? $group[0]['category_name'] : 'Uncategorized';

                                                if (count($group) > 0) {
                                                    echo '<optgroup label="' . $group_label . '">';
                                                    foreach ($group as $service) {
                                                        echo '<option value="' . $service['id'] . '">'
                                                        . $service['name'] . '</option>';
                                                    }
                                                    echo '</optgroup>';
                                                }
                                            }
                                        } else {
                                            foreach ($available_services as $service) {
                                                echo '<option value="' . $service['id'] . '">' . $service['name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="select-provider">
                                        <strong><?= lang('select_provider') ?></strong>
                                    </label>

                                    <select id="select-provider" class="col-xs-12 col-sm-4 form-control"></select>
                                </div>

                                <div id="service-description" style="display:none;"></div>
                            </div>
                        </div>

                        <div class="command-buttons">
                            <button type="button" id="button-next-1" class="btn button-next btn-primary"
                                    data-step_index="1">
                                        <?= lang('next') ?>
                                <span class="glyphicon glyphicon-forward"></span>
                            </button>
                        </div>
                    </div>

                    <!-- SELECT APPOINTMENT DATE -->

                    <div id="wizard-frame-2" class="wizard-frame" style="display:none;">
                        <div class="frame-container">

                            <h3 class="frame-title"><?= lang('step_two_title') ?></h3>

                            <div class="frame-content row">
                                <div class="col-xs-12 col-sm-6">
                                    <div id="select-date"></div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <div id="available-hours"></div>
                                </div>
                            </div>
                        </div>

                        <div class="command-buttons">
                            <button type="button" id="button-back-2" class="btn button-back btn-default" data-step_index="2">
                                <span class="glyphicon glyphicon-backward"></span>
                                <?= lang('back') ?>
                            </button>
                            <button type="button" id="button-next-2" class="btn button-next btn-primary" data-step_index="2">
                                <?= lang('next') ?>
                                <span class="glyphicon glyphicon-forward"></span>
                            </button>
                        </div>
                    </div>

                     <!-- APPOINTMENT DATA CONFIRMATION -->

                    <div id="wizard-frame-3" class="wizard-frame" style="display:none;">
                        
                        <div class="frame-container">
                            <h3 class="frame-title"><?= lang('step_four_title') ?></h3>
                            <div class="frame-content row">
                                <div id="appointment-details" class="col-xs-12 col-sm-6"></div>
                                <!--<div id="customer-details" class="col-xs-12 col-sm-6"></div>-->
                            </div>
                            <?php
                            if ($this->settings_model->get_setting('require_captcha') === '1'):
                                ?>
                                <div class="frame-content row">
                                    <div class="col-xs-12 col-sm-6">
                                        <h4 class="captcha-title">
                                            CAPTCHA
                                            <small class="glyphicon glyphicon-refresh"></small>
                                        </h4>
                                        <img class="captcha-image" src="<?= site_url('captcha') ?>">
                                        <input class="captcha-text" type="text" value="" />
                                        <span id="captcha-hint" class="help-block" style="opacity:0">&nbsp;</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="command-buttons">
                            <button type="button" id="button-back-3" class="btn button-back btn-default" data-step_index="3">
                                <span class="glyphicon glyphicon-backward"></span>
                                <?= lang('back') ?>
                            </button>
                            <form id="book-appointment-form" style="display:inline-block" method="post">
                                <button id="book-appointment-submit" type="button" class="btn btn-success">
                                    <span class="glyphicon glyphicon-ok"></span>
                                    <?= !$manage_mode ? lang('confirm') : lang('update') ?>
                                </button>
                                <input type="hidden" name="csrfToken" />
                                <input type="hidden" name="post_data" />
                            </form>
                        </div>
                    </div>

                    <div id="frame-footer">
                        Powered By
                        <a href="http://easyappointments.org" target="_blank">Easy!Appointments</a>
                        |
                        <span id="select-language" class="label label-success">
                            <?= ucfirst($this->config->item('language')) ?>
                        </span>
                        |
                        <a href="<?= site_url('backend'); ?>">
                            <?= $this->session->user_id ? lang('backend_section') : lang('login') ?>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <?php if ($display_cookie_notice === '1'): ?>
            <?php require 'cookie_notice_modal.php' ?>
        <?php endif ?>

        <?php if ($display_terms_and_conditions === '1'): ?>
            <?php require 'terms_and_conditions_modal.php' ?>
        <?php endif ?>

        <?php if ($display_privacy_policy === '1'): ?>
            <?php require 'privacy_policy_modal.php' ?>
        <?php endif ?>

        <script>
            var GlobalVariables = {
                availableServices: <?= json_encode($available_services) ?>,
                availableProviders: <?= json_encode($available_providers) ?>,
                baseUrl: <?= json_encode(config('base_url')) ?>,
                manageMode: <?= $manage_mode ? 'true' : 'false' ?>,
                customerToken: <?= json_encode($customer_token) ?>,
                dateFormat: <?= json_encode($date_format) ?>,
                timeFormat: <?= json_encode($time_format) ?>,
                displayCookieNotice: <?= json_encode($display_cookie_notice === '1') ?>,
                appointmentData: <?= json_encode($appointment_data) ?>,
                providerData: <?= json_encode($provider_data) ?>,
                customerData: <?= json_encode($customer_data) ?>,
                csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>
            };

            var EALang = <?= json_encode($this->lang->language) ?>;
            var availableLanguages = <?= json_encode($this->config->item('available_languages')) ?>;
        </script>

        <script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/cookieconsent/cookieconsent.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/datejs/date.js') ?>"></script>
        <script src="<?= asset_url('assets/js/frontend_book_api.js') ?>"></script>
        <script src="<?= asset_url('assets/js/frontend_book.js') ?>"></script>

        <script>
            $(document).ready(function () {
                FrontendBook.initialize(true, GlobalVariables.manageMode);
                GeneralFunctions.enableLanguageSelection($('#select-language'));
            });
        </script>

        <?php google_analytics_script($ac_id); ?>
    </body>
</html>
