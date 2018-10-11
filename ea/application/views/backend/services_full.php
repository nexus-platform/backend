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
        <script src="<?= asset_url('assets/js/backend_services_helper.js') ?>"></script>
        <script src="<?= asset_url('assets/js/backend_categories_helper.js') ?>"></script>
        <script src="<?= asset_url('assets/js/backend_services.js') ?>"></script>
        <script>
            var GlobalVariables = {
                csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
                baseUrl: <?= json_encode($base_url) ?>,
                dateFormat: <?= json_encode($date_format) ?>,
                timeFormat: <?= json_encode($time_format) ?>,
                services: <?= json_encode($services) ?>,
                categories: <?= json_encode($categories) ?>,
                user: {
                    id: <?= $user_id ?>,
                    email: <?= json_encode($user_email) ?>,
                    role_slug: <?= json_encode($role_slug) ?>,
                    privileges: <?= json_encode($privileges) ?>
                }
            };

            $(document).ready(function () {
                BackendServices.initialize(true);
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

        <div id="services-page" class="container-fluid backend-page">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#services" aria-controls="services" role="tab" data-toggle="tab"><?= lang('services') ?></a></li>
                <li role="presentation"><a href="#categories" aria-controls="categories" role="tab" data-toggle="tab"><?= lang('categories') ?></a></li>
            </ul>

            <div class="tab-content">

                <!-- SERVICES TAB -->

                <div role="tabpanel" class="tab-pane active" id="services">
                    <div class="row">
                        <div id="filter-services" class="filter-records column col-xs-12 col-sm-5">
                            <form>
                                <div class="input-group">
                                    <input type="text" class="key form-control">

                                    <span class="input-group-addon">
                                        <div>
                                            <button class="filter btn btn-default" type="submit" title="<?= lang('filter') ?>">
                                                <span class="glyphicon glyphicon-search"></span>
                                            </button>
                                            <button class="clear btn btn-default" type="button" title="<?= lang('clear') ?>">
                                                <span class="glyphicon glyphicon-repeat"></span>
                                            </button>
                                        </div>
                                    </span>
                                </div>
                            </form>

                            <h3><?= lang('services') ?></h3>
                            <div class="results"></div>
                        </div>

                        <div class="record-details column col-xs-12 col-sm-5">
                            <div class="btn-toolbar">
                                <div class="add-edit-delete-group btn-group">
                                    <button id="add-service" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-plus"></span>
                                        <?= lang('add') ?>
                                    </button>
                                    <button id="edit-service" class="btn btn-default" disabled="disabled">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                        <?= lang('edit') ?>
                                    </button>
                                    <button id="delete-service" class="btn btn-default" disabled="disabled">
                                        <span class="glyphicon glyphicon-remove"></span>
                                        <?= lang('delete') ?>
                                    </button>
                                </div>

                                <div class="save-cancel-group btn-group" style="display:none;">
                                    <button id="save-service" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-ok"></span>
                                        <?= lang('save') ?>
                                    </button>
                                    <button id="cancel-service" class="btn btn-default">
                                        <span class="glyphicon glyphicon-ban-circle"></span>
                                        <?= lang('cancel') ?>
                                    </button>
                                </div>
                            </div>

                            <h3><?= lang('details') ?></h3>

                            <div class="form-message alert" style="display:none;"></div>

                            <input type="hidden" id="service-id">

                            <div class="form-group">
                                <label for="service-name"><?= lang('name') ?> *</label>
                                <input id="service-name" class="form-control required" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="service-duration"><?= lang('duration_minutes') ?> *</label>
                                <input id="service-duration" class="form-control required" type="number" min="15">
                            </div>

                            <div class="form-group">
                                <label for="service-price"><?= lang('price') ?> *</label>
                                <input id="service-price" class="form-control required">
                            </div>

                            <div class="form-group">
                                <label for="service-currency"><?= lang('currency') ?></label>
                                <input id="service-currency" class="form-control" maxlength="32">
                            </div>

                            <div class="form-group">
                                <label for="service-category"><?= lang('category') ?></label>
                                <select id="service-category" class="form-control"></select>
                            </div>

                            <div class="form-group">
                                <label for="service-availabilities-type"><?= lang('availabilities_type') ?></label>
                                <select id="service-availabilities-type" class="form-control">
                                    <option value="<?= AVAILABILITIES_TYPE_FLEXIBLE ?>">
                                        <?= lang('flexible') ?>
                                    </option>
                                    <option value="<?= AVAILABILITIES_TYPE_FIXED ?>">
                                        <?= lang('fixed') ?>
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="service-attendants-number"><?= lang('attendants_number') ?> *</label>
                                <input id="service-attendants-number" class="form-control required" type="number" min="1">
                            </div>

                            <div class="form-group">
                                <label for="service-description"><?= lang('description') ?></label>
                                <textarea id="service-description" rows="4" class="form-control"></textarea>
                            </div>

                            <p id="form-message" class="text-danger">
                                <em><?= lang('fields_are_required') ?></em>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CATEGORIES TAB -->

                <div role="tabpanel" class="tab-pane" id="categories">
                    <div class="row">
                        <div id="filter-categories" class="filter-records column col-xs-12 col-sm-5">
                            <form class="input-append">
                                <div class="input-group">
                                    <input type="text" class="key form-control">

                                    <span class="input-group-addon">
                                        <div>
                                            <button class="filter btn btn-default" type="submit" title="<?= lang('filter') ?>">
                                                <span class="glyphicon glyphicon-search"></span>
                                            </button>
                                            <button class="clear btn btn-default" type="button" title="<?= lang('clear') ?>">
                                                <span class="glyphicon glyphicon-repeat"></span>
                                            </button>
                                        </div>
                                    </span>
                                </div>
                            </form>

                            <h3><?= lang('categories') ?></h3>
                            <div class="results"></div>
                        </div>

                        <div class="record-details col-xs-12 col-sm-5">
                            <div class="btn-toolbar">
                                <div class="add-edit-delete-group btn-group">
                                    <button id="add-category" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-plus glyphicon glyphicon-white"></span>
                                        <?= lang('add') ?>
                                    </button>
                                    <button id="edit-category" class="btn btn-default" disabled="disabled">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                        <?= lang('edit') ?>
                                    </button>
                                    <button id="delete-category" class="btn btn-default" disabled="disabled">
                                        <span class="glyphicon glyphicon-remove"></span>
                                        <?= lang('delete') ?>
                                    </button>
                                </div>

                                <div class="save-cancel-group btn-group" style="display:none;">
                                    <button id="save-category" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-ok glyphicon glyphicon-white"></span>
                                        <?= lang('save') ?>
                                    </button>
                                    <button id="cancel-category" class="btn btn-default">
                                        <span class="glyphicon glyphicon-ban-circle"></span>
                                        <?= lang('cancel') ?>
                                    </button>
                                </div>
                            </div>

                            <h3><?= lang('details') ?></h3>

                            <div class="form-message alert" style="display:none;"></div>

                            <input type="hidden" id="category-id">

                            <div class="form-group">
                                <label for="category-name"><?= lang('name') ?> *</label>
                                <input id="category-name" class="form-control required">
                            </div>

                            <div class="form-group">
                                <label for="category-description"><?= lang('description') ?></label>
                                <textarea id="category-description" rows="4" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include_once 'footer.php'; ?>
