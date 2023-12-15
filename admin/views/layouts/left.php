<?php

use common\config\includes\P;
use common\models\Account;
use common\models\Division;
use common\models\RemovalRequest;
use common\models\RepairRequest;
use common\models\users\Admin;
use dmstr\widgets\Menu;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;

$removalRequestCount = RemovalRequest::find()->count();

?>
<aside class="main-sidebar">
    <section class="sidebar">
        <?php if (!Yii::$app->getUser()->isGuest) { ?>
        <?php
            /* @var $user Admin */
            $user = Yii::$app->getUser()->getIdentity();
            ?>
        <a href="<?= Url::to(['/site/profile']) ?>" class="user-panel display-block">
            <div class="pull-left image">
                <?php $imagePath = $user->image_thumb_path;
                    if (file_exists($imagePath)) {
                        $imageUrl = $user->image_thumb_url;
                    } else {
                        $imageUrl = Yii::getAlias('@staticWeb') . '/images/user-default.jpg';
                    } ?>
                <img src="<?= $imageUrl ?>" class="img-circle" alt="<?= $user->name ?>" />
            </div>
            <div class="pull-left info">
                <p>
                    <?= $user->name ?>
                </p>
                <sup><i class="fa fa-circle text-green"></i>
                    <?= Yii::t("app", "Active"); ?>
                </sup>
            </div>
        </a>
        <?php } ?>
        <?= Html::input("text", "search-sidebar", null, [
            'class' => 'form-control',
            'id' => 'search-sidebar',
            'placeholder' => 'Press / to Search menu'
        ]) ?>

        <?=
        Menu::widget(
            [
                'encodeLabels' => false,
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items' => [

                    [
                        'label' => Yii::t("app", 'Work Orders'),
                        'icon' => 'wrench',
                        'visible' => P::c(P::REPAIR_SECTION_SECTION_ENABLED),
                        'items' => [
                            ['label' => Yii::t("app", 'Work Dashboard'), 'icon' => 'wrench', 'url' => ['/site/works-dashboard'], 'visible' => P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_VIEW)],
                            ['label' => Yii::t("app", "Completed Work"), 'icon' => 'check', 'url' => ['/repair-request/index', 'RepairRequestSearch[status]' => RepairRequest::STATUS_COMPLETED], 'visible' => P::c(P::REPAIR_COMPLETED_REPAIRS_PAGE_VIEW)],
                            [
                                'label' => Yii::t("app", "External Work Order"),
                                'icon' => 'check',
                                'url' => ['/external-work-order/index'],
                                'visible' => P::c(P::REPAIR_EXTERNAL_WORK_ORDER_PAGE_VIEW) &&
                                    (Yii::$app->user->identity->division_id == Division::DIVISION_VILLA || Yii::$app->user->identity->division_id == '')
                            ],
                        ]
                    ],

                    ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],

                    // [
                    //     'label'   => Yii::t("app", 'Works'), 'icon' => 'cogs',
                    //     'visible' => P::c(P::WORK_SECTION_SECTION_ENABLED),
                    //     'items'   => [
                    //         ['label' => Yii::t("app", 'Works Dashboard'), 'icon' => 'cogs', 'url' => ['/site/works-dashboard'], 'visible' => P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_VIEW)],
                    //         ['label' => Yii::t("app", "Completed Works"), 'icon' => 'check-square-o', 'url' => ['/repair-request/index', 'RepairRequestSearch[status]' => RepairRequest::STATUS_COMPLETED, 'RepairRequestSearch[type]' => RepairRequest::TYPE_SCHEDULED], 'visible' => P::c(P::WORK_COMPLETED_WORK_PAGE_VIEW)],
                    //     ]
                    // ],

                    ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],

                    [
                        'label' => Yii::t("app", 'Reports'),
                        'icon' => 'tasks',
                        'visible' => P::c(P::REPAIR_SUMMARY_DASHBOARD_PAGE_VIEW) || P::c(P::REPAIR_MONTHLY_DASHBOARD_PAGE_VIEW) || P::c(P::REPORT_LABOR_CHARGE_PAGE_VIEW),
                        'items' => [
                            ['label' => Yii::t("app", 'Summary Dashboard'), 'icon' => 'wrench', 'url' => ['/site/summary-dashboard'], 'visible' => P::c(P::REPAIR_SUMMARY_DASHBOARD_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'Monthly Dashboard'), 'icon' => 'wrench', 'url' => ['/site/monthly-dashboard', '_s' => date("Y-m-d"), '_e' => date("Y-m-t")], 'visible' => P::c(P::REPAIR_MONTHLY_DASHBOARD_PAGE_VIEW)],

                            ['label' => Yii::t("app", 'Labor Charge'), 'icon' => 'wrench', 'url' => ['/site/labor-charge'], 'visible' => P::c(P::REPORT_LABOR_CHARGE_PAGE_VIEW)],

                            // ['label' => Yii::t("app", 'Maintenance Progress'), 'icon' => 'tasks', 'url' => ['/maintenance/progress'], 'visible' => P::c(P::MAINTENANCE_PROGRESS_PAGE_VIEW)],
                            // ['label' => Yii::t("app", 'Maintenance Planning'), 'icon' => 'map', 'url' => ['/maintenance/planning'], 'visible' => P::c(P::MAINTENANCE_PLANNING_PAGE_VIEW)],
                        ]
                    ],

                    ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],

                    [
                        'label' => Yii::t("app", 'Management'),
                        'icon' => 'cogs',
                        'visible' => P::c(P::MANAGEMENT_SECTION_SECTION_ENABLED) || P::c(P::CONFIGURATIONS_CATEGORY_PAGE_VIEW) || P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_AUDIT) || P::c(P::MANAGEMENT_LOCATION_PAGE_AUDIT) || P::c(P::MANAGEMENT_EQUIPMENT_PAGE_AUDIT) || P::c(P::MANAGEMENT_TECHNICIAN_PAGE_AUDIT) || P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_AUDIT) || P::c(P::ADMINS_ADMIN_PAGE_AUDIT) || P::c(P::CONFIGURATIONS_CATEGORY_PAGE_AUDIT)
                            || P::c(P::CONFIGURATIONS_CATEGORY_PAGE_AUDIT)
                            || P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_AUDIT)
                            || P::c(P::CONFIGURATIONS_PROFESSION_PAGE_AUDIT)
                            || P::c(P::CONFIGURATIONS_SECTOR_PAGE_AUDIT)
                            || P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_AUDIT),
                        'items' => [
                            //                    ['label' => Yii::t("app", 'KPIs'), 'icon' => 'dashboard', 'url' => ['/site/kpis']],
                            ['label' => Yii::t("app", "Technicians"), 'icon' => 'user-secret', 'url' => ['/technician/index'], 'visible' => P::c(P::MANAGEMENT_TECHNICIAN_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'Manage Technicians Shifts'), 'icon' => 'clock-o', 'url' => ['/technician-shift/index'], 'visible' => P::c(P::MANAGEMENT_TECHNICIAN_SHIFTS_PAGE_VIEW)],
                            // ['label' => Yii::t("app", "Workers"), 'icon' => 'users', 'url' => ['/worker/index'], 'visible' => P::c(P::MANAGEMENT_TECHNICIAN_PAGE_VIEW)],
                            //                     'items' => [
                            // ['label' => Yii::t("app", "Customers"), 'icon' => 'users', 'url' => ['/customer/index'], 'visible' => P::c(P::MISC_MANAGE_CUSTOMERS)],
                            ['label' => Yii::t("app", "Locations"), 'icon' => 'building', 'url' => ['/location/index'], 'visible' => P::c(P::MANAGEMENT_LOCATION_PAGE_VIEW)],
                            ['label' => Yii::t("app", "Segment Path"), 'icon' => 'caret-square-o-up', 'url' => ['/segment-path/index'], 'visible' => P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_VIEW)],
                            ['label' => Yii::t("app", "Equipment"), 'icon' => 'caret-square-o-up', 'url' => ['/equipment/index'], 'visible' => P::c(P::MANAGEMENT_EQUIPMENT_PAGE_VIEW)],
                            ['label' => Yii::t("app", "Equipment Type"), 'icon' => 'caret-square-o-up', 'url' => ['/equipment-type/index'], 'visible' => P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_VIEW)],
                            // ['label' => Yii::t("app", "Equipment Path"), 'icon' => 'caret-square-o-up', 'url' => ['/equipment-path/index'], 'visible' => P::c(P::MANAGEMENT_EQUIPMENT_PAGE_VIEW)],
                            // ['label' => Yii::t("app", "Users"), 'icon' => 'user', 'url' => ['/user/index'], 'visible' => P::c(P::MANAGEMENT_USER_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'Category'), 'icon' => 'server', 'url' => ['/category/index'], 'visible' => P::c(P::CONFIGURATIONS_CATEGORY_PAGE_VIEW)],
                            ['label' => 'Login Audit', 'icon' => 'gear', 'url' => ['/login-audit/index'], 'visible' => P::c(P::MANAGEMENT_LOGIN_AUDIT_PAGE_VIEW)],
                            [
                                'label' => 'Audit Trail', 'icon' => 'chain', 'url' => ['/user-audit/index'], 'visible' => P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_AUDIT) || P::c(P::MANAGEMENT_LOCATION_PAGE_AUDIT) || P::c(P::MANAGEMENT_EQUIPMENT_PAGE_AUDIT) || P::c(P::MANAGEMENT_TECHNICIAN_PAGE_AUDIT) || P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_AUDIT) || P::c(P::ADMINS_ADMIN_PAGE_AUDIT) || P::c(P::CONFIGURATIONS_CATEGORY_PAGE_AUDIT)
                                    || P::c(P::CONFIGURATIONS_CATEGORY_PAGE_AUDIT)
                                    || P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_AUDIT)
                                    || P::c(P::CONFIGURATIONS_PROFESSION_PAGE_AUDIT)
                                    || P::c(P::CONFIGURATIONS_SECTOR_PAGE_AUDIT)
                                    || P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_AUDIT)
                            ],
                            ['label' => 'Blocked Ip', 'icon' => 'file-code-o', 'url' => ['/blocked-ip/index'], 'visible' => Yii::$app->user->can('developer')],
                            ['label' => Yii::t("app", 'Coordinates Issues'), 'icon' => 'map', 'url' => ['/coordinates-issue/index'], 'visible' => P::c(P::MANAGEMENT_COORDINATES_ISSUES_PAGE_VIEW)],
                            // ]
                            // ],
                            // [
                            //     'label'   => Yii::t("app", 'Line Items'), 'icon' => 'th-list',
                            //     'visible' => P::c(P::MISC_MANAGE_LINE_ITEMS),
                            //     'items'   => [
                            //         ['label' => Yii::t("app", "Cause Codes"), 'icon' => 'code', 'url' => ['/cause-code/index']],
                            //         ['label' => Yii::t("app", "Object Category"), 'icon' => 'cube', 'url' => ['/object-category/index']],
                            //         ['label' => Yii::t("app", "Object Codes"), 'icon' => 'cubes', 'url' => ['/object-code/index']],
                            //         ['label' => Yii::t("app", "Damage Codes"), 'icon' => 'fire', 'url' => ['/damage-code/index']],
                            //         ['label' => Yii::t("app", "Manufacturer"), 'icon' => 'industry', 'url' => ['/manufacturer/index']],
                            //         //['label' => Yii::t("app", "Line Items"), 'icon' => 'th-list', 'url' => ['/line-item/index']],
                            //     ]
                            // ],
                            // ['label' => Yii::t("app", "Equipments Categories"), 'icon' => 'caret-square-o-up', 'url' => ['/equipment-category/index'], 'visible' => P::c(P::MANAGEMENT_EQUIPMENT_PAGE)],
                            // ['label' => Yii::t("app", "Equipments Types"), 'icon' => 'caret-square-o-up', 'url' => ['/equipment-type/index'], 'visible' => P::c(P::MANAGEMENT_EQUIPMENT_PAGE)],
                            ['label' => Yii::t("app", "Problems"), 'icon' => 'ban', 'url' => ['/problem/index'], 'visible' => P::c(P::MANAGEMENT_PROBLEM_PAGE_VIEW)],
                            // ['label' => Yii::t("app", "Technicians Locations"), 'icon' => 'map-marker', 'url' => ['/technician-location/map'], 'visible' => P::c(P::MANAGEMENT_TECHNICIAN_PAGE_LOCATIONS)],
                            // ['label' => Yii::t("app", 'Equipments Map'), 'icon' => 'building', 'url' => ['/equipment/map'], 'visible' => P::c(P::MANAGEMENT_LOCATION_PAGE_MAP)],
                            // ['label' => Yii::t("app", "News"), 'icon' => 'newspaper-o', 'url' => ['/article/index'], 'visible' => P::c(P::MISC_MANAGE_CUSTOMERS)],
                            // ['label' => Yii::t("app", "Removal Requests") . ($removalRequestCount > 0 ? Html::tag("span", $removalRequestCount, ['class' => 'label pull-right bg-red']) : ""), 'icon' => 'ban', 'url' => ['/removal-request/index'], 'visible' => P::c(P::MISC_MANAGE_REMOVAL_REQUESTS)],
                            // ['label' => Yii::t("app", "All Barcodes"), 'icon' => 'list-ol', 'url' => ['/equipment-maintenance-barcode/index'], 'visible' => P::c(P::ALL_BARCODES_PAGE_VIEW)],
                            // ['label' => Yii::t("app", "Working Hours"), 'icon' => 'clock-o', 'url' => ['working-hours/index'], 'visible' => P::c(P::MISC_MANAGE_WORKING_HOURS)],
                            // ['label' => Yii::t("app", "Deleted Services"), 'icon' => 'trash', 'url' => ['deleted-service/index'], 'visible' => P::c(P::DEVELOPER)],

                        ]
                    ],

                    ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],
                    [
                        'label' => Yii::t("app", 'PPM'),
                        'icon' => 'calendar-times-o',
                        'visible' => P::c(P::PPM_SECTION_SECTION_ENABLED),
                        'items' => [
                            [
                                'label' => Yii::t("app", "Mall"),
                                'visible' => P::c(P::PPM_MALL_PPM_SERVICES_VIEW) || P::c(P::PPM_MALL_PPM_TASKS_VIEW),
                                'items' => [
                                    ['label' => Yii::t("app", 'Mall PPM Services'), 'icon' => '', 'url' => ['/mall-ppm-tasks/services'], 'visible' => P::c(P::PPM_MALL_PPM_SERVICES_VIEW)],
                                    ['label' => Yii::t("app", 'Mall PPM Tasks'), 'icon' => '', 'url' => ['/mall-ppm-tasks/index'], 'visible' => P::c(P::PPM_MALL_PPM_TASKS_VIEW)],
                                ]
                            ],
                            // PLANT PPM
                            [
                                'label' => Yii::t("app", "Plant"),
                                'visible' => P::c(P::PPM_PLANT_PPM_SERVICES_VIEW) || P::c(P::PPM_PLANT_PPM_TASKS_VIEW),
                                'items' => [
                                    ['label' => Yii::t("app", 'PPM Services / Checklist'), 'icon' => '', 'url' => ['/plant-ppm-tasks/services'], 'visible' => P::c(P::PPM_PLANT_PPM_SERVICES_VIEW)],
                                    ['label' => Yii::t("app", 'Plant PPM Tasks'), 'icon' => '', 'url' => ['/plant-ppm-tasks/index'], 'visible' => P::c(P::PPM_PLANT_PPM_TASKS_VIEW)],
                                ]
                            ],
                            // VILLA PPM
                            [
                                'label' => Yii::t("app", "Villa"),
                                'visible' => P::c(P::PPM_VILLA_PPM_TEMPLATES_VIEW) || P::c(P::PPM_VILLA_PPM_TASKS_VIEW),
                                'items' => [
                                    ['label' => Yii::t("app", 'Villa PPM Templates'), 'icon' => '', 'url' => ['/villa-ppm-templates/index'], 'visible' => P::c(P::PPM_VILLA_PPM_TEMPLATES_VIEW)],
                                    ['label' => Yii::t("app", 'Villa PPM Tasks'), 'icon' => '', 'url' => ['/villa-ppm-tasks/index'], 'visible' => P::c(P::PPM_VILLA_PPM_TASKS_VIEW)],
                                ]
                            ],
                        ]
                    ],
                    ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],

                    [
                        'label' => Yii::t("app", 'Import'),
                        'icon' => 'download',
                        'visible' => P::c(P::IMPORT_SECTION_SECTION_ENABLED),
                        'items' => [
                            ['label' => Yii::t("app", "Import Categories"), 'url' => ['/import/categories'], 'visible' => P::c(P::IMPORT_TECHNICIAN_PAGE_IMPORT),],
                            ['label' => Yii::t("app", "Import Equipment Types"), 'url' => ['/import/equipment-types'], 'visible' => P::c(P::IMPORT_TECHNICIAN_PAGE_IMPORT),],
                            ['label' => Yii::t("app", "Import Locations"), 'url' => ['/import/locations'], 'visible' => P::c(P::IMPORT_LOCATION_PAGE_IMPORT),],
                            ['label' => Yii::t("app", "Import Equipments"), 'url' => ['/import/equipments'], 'visible' => P::c(P::IMPORT_TECHNICIAN_PAGE_IMPORT),],
                            ['label' => Yii::t("app", "Import Location Equipments"), 'url' => ['/import/location-equipments'], 'visible' => P::c(P::IMPORT_TECHNICIAN_PAGE_IMPORT),],
                        ]
                    ],

                    ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],

                    ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],

                    [
                        'label' => Yii::t("app", 'Backend Users'),
                        'icon' => 'user-o',
                        'visible' => P::c(P::ADMINS_SECTION_SECTION_ENABLED),
                        'items' => [
                            ['label' => Yii::t("app", "Account Type"), 'icon' => 'user-secret', 'url' => ['/account-type/index'], 'visible' => P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_VIEW)],

                            ['label' => Yii::t("app", 'Manage Users'), 'icon' => 'user-o', 'url' => ['/admin/index'], 'visible' => P::c(P::ADMINS_ADMIN_PAGE_VIEW)],

                            //----------------------------------------------------------
                            [
                                'label' => Yii::t("app", 'Roles & Assignments'),
                                'icon' => 'unlock-alt',
                                'url' => '#',
                                'visible' => P::c(P::ADMINS_ROLE_PAGE_VIEW),
                                'items' => [
                                    // ['label' => Yii::t("app", 'Roles Assignments'), 'icon' => '', 'url' => ['/rbac/assignment/index'], 'visible' => P::c(P::ADMINS_ROLE_ASSIGNMENT_PAGE_VIEW)],
                                    ['label' => Yii::t("app", 'Roles'), 'icon' => '', 'url' => ['/rbac/role/index'], 'visible' => P::c(P::ADMINS_ROLE_PAGE_VIEW)],
                                    // ['label' => Yii::t("app", 'Permissions'), 'icon' => '', 'url' => ['/rbac/permission/index'], 'visible' => P::c(P::ADMINS_PERMISSION_PAGE_VIEW)],
                                    //['label' => Yii::t("app", 'Rules'), 'icon' => 'gear', 'url' => ['/rbac/rule'], 'visible' => true],
                                ]
                            ],
                            //----------------------------------------------------------
                            ['label' => Yii::t("app", 'Settings'), 'icon' => 'gear', 'url' => ['/settings/index'], 'visible' => P::c(P::ADMINS_SETTINGS_PAGE_VIEW)],

                        ]
                    ],

                    // [
                    //     'label'   => Yii::t("app", 'Translations'), 'icon' => 'language',
                    //     'visible' => false,
                    //     'items'   => [
                    //         ['label' => Yii::t('language', 'List of languages'), 'url' => ['/translatemanager/language/list'], 'visible' => false],
                    //         //['label' => Yii::t('language', 'Create'), 'url' => ['/translatemanager/language/create']],
                    //         ['label' => Yii::t('language', 'Scan & Optimize'), 'items' => [
                    //             ['label' => Yii::t('language', 'Scan'), 'url' => ['/translatemanager/language/scan'], 'visible' => false],
                    //             ['label' => Yii::t('language', 'Optimize'), 'url' => ['/translatemanager/language/optimizer'], 'visible' => false],
                    //         ]],
                    //         ['label' => Yii::t('language', 'Im-/Export'), 'items' => [
                    //             ['label' => Yii::t('language', 'Import'), 'url' => ['/translatemanager/language/import'], 'visible' => false],
                    //             ['label' => Yii::t('language', 'Export'), 'url' => ['/translatemanager/language/export'], 'visible' => false],
                    //         ]],
                    //     ]
                    // ],

                    ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],

                    [
                        'label' => Yii::t("app", 'Configuration'),
                        'visible' => P::c(P::CONFIGURATIONS_SECTION_SECTION_ENABLED),
                        'url' => '#',
                        'icon' => 'gear',
                        'items' => [
                            ['label' => Yii::t("app", 'Engine Oil Types'), 'icon' => 'tint', 'url' => ['/engine-oil-types/index'], 'visible' => P::c(P::CONFIGURATIONS_ENGINE_OIL_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'Division'), 'icon' => 'home', 'url' => ['/division/index'], 'visible' => P::c(P::CONFIGURATIONS_DIVISION_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'Profession'), 'icon' => 'user', 'url' => ['/profession/index'], 'visible' => P::c(P::CONFIGURATIONS_PROFESSION_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'Main Sector'), 'icon' => 'bank', 'url' => ['/main-sector/index'], 'visible' => P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_VIEW)],
                            ['label' => Yii::t("app", "Sectors"), 'icon' => 'map', 'url' => ['/sector/index'], 'visible' => P::c(P::CONFIGURATIONS_SECTOR_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'Shift'), 'icon' => 'clock-o', 'url' => ['/shift/index'], 'visible' => P::c(P::CONFIGURATIONS_SHIFT_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'Country'), 'icon' => 'globe', 'url' => ['/country/index'], 'visible' => P::c(P::CONFIGURATIONS_COUNTRY_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'State'), 'icon' => 'address-book', 'url' => ['/state/index'], 'visible' => P::c(P::CONFIGURATIONS_STATE_PAGE_VIEW)],
                            ['label' => Yii::t("app", 'City'), 'icon' => 'bank', 'url' => ['/city/index'], 'visible' => P::c(P::CONFIGURATIONS_CITY_PAGE_VIEW)],

                        ]
                    ],

                    [
                        'label' => Yii::t("app", 'Development'),
                        'icon' => 'file-code-o',
                        'visible' => P::c(P::DEVELOPMENT_SECTION_SECTION_ENABLED),
                        'items' => [
                            ['label' => 'Clear Cache', 'icon' => 'square-o', 'url' => ['/developer/clear-cache'], 'visible' => P::c(P::DEVELOPMENT_CLEAR_CACHE_PAGE_VIEW)],
                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'], 'visible' => P::c(P::DEVELOPMENT_GII_PAGE_VIEW)],
                            [
                                'label' => Yii::t("app", 'Debug'),
                                'icon' => 'life-bouy',
                                'visible' => P::c(P::DEVELOPMENT_DEBUG_PAGE_VIEW),
                                'items' => [
                                    ['label' => 'Admin Debug', 'icon' => 'life-bouy', 'url' => ['/debug'], 'visible' => true],
                                    ['label' => 'Api Debug', 'icon' => 'life-bouy', 'url' => ['/api-debug'], 'visible' => true],
                                    //['label' => 'User Debug', 'icon' => 'life-bouy', 'url' => ['/user-debug'], 'visible' => P::c(P::DEVELOPER)],
                                    ['label' => 'Technician Debug', 'icon' => 'life-bouy', 'url' => ['/technician-debug'], 'visible' => true],
                                ]
                            ],

                        ]
                    ],
                ],
            ]
        )
        ?>

    </section>

    <!-- /menu footer buttons -->
    <div class="sidebar-footer hidden-small">
        <?php if (P::c(P::ADMINS_SETTINGS_PAGE_VIEW)) { ?>
        <?=
            Html::a(
                '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>',
                ['/settings/index'],
                ['data-toggle' => "tooltip", 'data-placement' => "top", 'title' => "Settings"]
            )
            ?>
        <?php } else { ?>
        <?=
            Html::a(
                '<span class="glyphicon glyphicon-home" aria-hidden="true"></span>',
                ['/site/index'],
                ['data-toggle' => "tooltip", 'data-placement' => "top", 'title' => "Home"]
            )
            ?>
        <?php } ?>
        <a data-toggle="tooltip" data-placement="top" title="Full Screen" id="request-fullscreen">
            <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
        </a>
        <?=
        Html::a(
            '<span class="glyphicon glyphicon-user" aria-hidden="true"></span>',
            ['/site/profile'],
            ['data-toggle' => "tooltip", 'data-placement' => "top", 'title' => "Profile"]
        )
        ?>
        <?=
        Html::a('<span class="glyphicon glyphicon-off" aria-hidden="true"></span>', Url::to(['site/logout']), [
            'data-method' => 'POST',
            'data-toggle' => "tooltip",
            'data-placement' => "top",
            'title' => "Logout"
        ])
        ?>
    </div>
</aside>


<script type="text/javascript">
<?php ob_start() ?>
$("#request-fullscreen").click(function() {
    if (screenfull.enabled) {
        screenfull.toggle();
    }
});

$("#search-sidebar").on("keyup", function(e) {
    var query = $(this).val();
    if (query.trim().length > 0) {
        $(".sidebar-menu li").each(function(index, col) {
            if ($(col).text().toLowerCase().includes(query.toLowerCase())) {
                $(col).removeClass("d-none");
            } else {
                $(col).addClass("d-none");
            }
        })
        $(".treeview-menu").css("display", 'block');
    } else {
        $(".sidebar-menu li").removeClass("d-none");
        $(".treeview-menu").css("display", 'none');
    }
});

function _focusSearch() {
    setTimeout(() => {
        $("#search-sidebar").focus();
    }, 50)
}

Mousetrap.bind('/', _focusSearch);
setTimeout(() => {
    $('.sidebar').slimScroll({
        scrollTo: $(".sidebar li.active").offset().top - 70
    }, 350);
}, 100)

<?php $js = ob_get_clean() ?>
<?php $this->registerJs($js) ?>
</script>