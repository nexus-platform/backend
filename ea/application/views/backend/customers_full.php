<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= $company_name ?> | Easy!Appointments</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

        <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">

        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/trumbowyg/ui/trumbowyg.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/backend.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">

        <script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/datejs/date.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/jquery-mousewheel/jquery.mousewheel.js') ?>"></script>
        <script src="<?= asset_url('assets/ext/trumbowyg/trumbowyg.min.js') ?>"></script>

        <script>
            // Global JavaScript Variables - Used in all backend pages.
            var availableLanguages = <?= json_encode($this->config->item('available_languages')) ?>;
            var EALang = <?= json_encode($this->lang->language) ?>;
        </script>
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
        <script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui-timepicker-addon.js') ?>"></script>
        <script src="<?= asset_url('assets/js/backend_customers_helper.js') ?>"></script>
        <script src="<?= asset_url('assets/js/backend_customers.js') ?>"></script>
        <script>
            var GlobalVariables = {
                csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
                availableProviders: <?= json_encode($available_providers) ?>,
                availableServices: <?= json_encode($available_services) ?>,
                secretaryProviders: <?= json_encode($secretary_providers) ?>,
                dateFormat: <?= json_encode($date_format) ?>,
                timeFormat: <?= json_encode($time_format) ?>,
                baseUrl: <?= json_encode($base_url) ?>,
                customers: <?= json_encode($customers) ?>,
                user: {
                    id: <?= $user_id ?>,
                    email: <?= json_encode($user_email) ?>,
                    role_slug: <?= json_encode($role_slug) ?>,
                    privileges: <?= json_encode($privileges) ?>
                }
            };

            $(document).ready(function () {
                BackendCustomers.initialize(true);
            });
        </script>
    </head>

    <body onload="iframe_resize();">
        <nav id="header" class="navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <div id="header-logo" class="navbar-brand">
                        <img src="<?= base_url('assets/img/logo.png') ?>">
                        <span><?= $company_name ?></span>
                    </div>

                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-menu" 
                            aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div id="header-menu" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <?php $hidden = ($privileges[PRIV_APPOINTMENTS]['view'] == TRUE) ? '' : 'hidden' ?>
                        <?php $active = ($active_menu == PRIV_APPOINTMENTS) ? 'active' : '' ?>
                        <li class="<?= $active . $hidden ?>">
                            <a href="<?= site_url('backend') ?>" class="menu-item"
                               title="<?= lang('manage_appointment_record_hint') ?>">
                                   <?= lang('calendar') ?>
                            </a>
                        </li>

                        <?php $hidden = ($privileges[PRIV_CUSTOMERS]['view'] == TRUE) ? '' : 'hidden' ?>
                        <?php $active = ($active_menu == PRIV_CUSTOMERS) ? 'active' : '' ?>
                        <li class="<?= $active . $hidden ?>">
                            <a href="<?= site_url('backend/customers') ?>" class="menu-item"
                               title="<?= lang('manage_customers_hint') ?>">
                                   <?= lang('customers') ?>
                            </a>
                        </li>

                        <?php $hidden = ($privileges[PRIV_SERVICES]['view'] == TRUE) ? '' : 'hidden' ?>
                        <?php $active = ($active_menu == PRIV_SERVICES) ? 'active' : '' ?>
                        <li class="<?= $active . $hidden ?>">
                            <a href="<?= site_url('backend/services') ?>" class="menu-item"
                               title="<?= lang('manage_services_hint') ?>">
                                   <?= lang('services') ?>
                            </a>
                        </li>

                        <?php $hidden = ($privileges[PRIV_USERS]['view'] == TRUE) ? '' : 'hidden' ?>
                        <?php $active = ($active_menu == PRIV_USERS) ? 'active' : '' ?>
                        <li class="<?= $active . $hidden ?>">
                            <a href="<?= site_url('backend/users') ?>" class="menu-item"
                               title="<?= lang('manage_users_hint') ?>">
                                   <?= lang('users') ?>
                            </a>
                        </li>

                        <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE || $privileges[PRIV_USER_SETTINGS]['view'] == TRUE) ? '' : 'hidden'
                        ?>
                        <?php $active = ($active_menu == PRIV_SYSTEM_SETTINGS) ? 'active' : '' ?>
                        <li class="<?= $active . $hidden ?>">
                            <a href="<?= site_url('backend/settings') ?>" class="menu-item"
                               title="<?= lang('settings_hint') ?>">
                                   <?= lang('settings') ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="notification" style="display: none;"></div>

        <div id="loading" style="display: none;">
            <div class="any-element animation is-loading">
                &nbsp;
            </div>
        </div>

        <div id="customers-page" class="container-fluid backend-page">
            <div class="row">
                <div id="filter-customers" class="filter-records column col-xs-12 col-sm-5">
                    <form>
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <div class="input-group-addon">
                                <div>
                                    <button class="filter btn btn-default" type="submit" title="<?= lang('filter') ?>">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                    <button class="clear btn btn-default" type="button" title="<?= lang('clear') ?>">
                                        <span class="glyphicon glyphicon-repeat"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <h3><?= lang('customers') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details col-xs-12 col-sm-7">
                    <div class="btn-toolbar">
                        <div id="add-edit-delete-group" class="btn-group">
                            <?php if ($privileges[PRIV_CUSTOMERS]['add'] === TRUE): ?>
                                <!--<button id="add-customer" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-plus"></span>
                                    <?= lang('add') ?>
                                </button>-->
                            <?php endif ?>

                            <?php if ($privileges[PRIV_CUSTOMERS]['edit'] === TRUE): ?>
                                <button id="edit-customer" class="btn btn-default" disabled="disabled">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                    <?= lang('edit') ?>
                                </button>
                            <?php endif ?>

                            <?php if ($privileges[PRIV_CUSTOMERS]['delete'] === TRUE): ?>
                                <button id="delete-customer" class="btn btn-default" disabled="disabled">
                                    <span class="glyphicon glyphicon-remove"></span>
                                    <?= lang('delete') ?>
                                </button>
                            <?php endif ?>
                        </div>

                        <div id="save-cancel-group" class="btn-group" style="display:none;">
                            <button id="save-customer" class="btn btn-primary">
                                <span class="glyphicon glyphicon-ok"></span>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-customer" class="btn btn-default">
                                <i class="glyphicon glyphicon-ban-circle"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <input id="customer-id" type="hidden">

                    <div class="row">
                        <div class="col-xs-12 col-sm-6" style="margin-left: 0;">
                            <h3><?= lang('details') ?></h3>

                            <div id="form-message" class="alert" style="display:none;"></div>

                            <div class="form-group">
                                <label class="control-label" for="first-name"><?= lang('first_name') ?> *</label>
                                <input id="first-name" class="form-control required">
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="last-name"><?= lang('last_name') ?> *</label>
                                <input id="last-name" class="form-control required">
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="email"><?= lang('email') ?> *</label>
                                <input id="email" class="form-control required">
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="phone-number"><?= lang('phone_number') ?> *</label>
                                <input id="phone-number" class="form-control required">
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="address"><?= lang('address') ?></label>
                                <input id="address" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="city"><?= lang('city') ?></label>
                                <input id="city" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="zip-code"><?= lang('zip_code') ?></label>
                                <input id="zip-code" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="notes"><?= lang('notes') ?></label>
                                <textarea id="notes" rows="4" class="form-control"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label" for="status">Status</label>
                                <select id="status" class="form-control">
                                    <option></option>
                                    <option value="0">Disabled</option>
                                    <option value="1">Enabled</option>
                                </select>
                            </div>

                            <p class="text-center">
                                <em id="form-message" class="text-danger"><?= lang('fields_are_required') ?></em>
                            </p>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <h3><?= lang('appointments') ?></h3>
                            <div id="customer-appointments" class="well"></div>
                            <div id="appointment-details" class="well hidden"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include_once 'footer.php'; ?>
