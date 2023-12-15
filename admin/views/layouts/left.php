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
                            'label' => Yii::t("app", 'Managements'),
                            'icon' => 'cogs',
                            'visible' => true,
                            'items' => [
                                ['label' => Yii::t("app", 'Import Pdf'), 'icon' => 'file-pdf-o', 'url' => ['/site/index'], 'visible' => P::c(P::REPAIR_SUMMARY_DASHBOARD_PAGE_VIEW)],
                                ['label' => Yii::t("app", 'PdfGPT'), 'icon' => 'comments', 'url' => ['/site/pdf-gpt'], 'visible' => P::c(P::REPAIR_MONTHLY_DASHBOARD_PAGE_VIEW)],
                            ]
                        ],

                        ['label' => '', 'options' => ['class' => 'header separator'], 'visible' => true],

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
    $("#request-fullscreen").click(function () {
        if (screenfull.enabled) {
            screenfull.toggle();
        }
    });

    $("#search-sidebar").on("keyup", function (e) {
        var query = $(this).val();
        if (query.trim().length > 0) {
            $(".sidebar-menu li").each(function (index, col) {
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