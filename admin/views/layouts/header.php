<?php

use common\components\extensions\LanguageSwitcher;
use common\components\notification\widgets\AdminNotificationsPanelWidget;
use common\components\notification\widgets\NotificationsPanelWidget;
use common\components\rbac\models\AssignmentForm;
use common\models\Account;
use common\models\Division;
use common\models\MainSector;
use common\widgets\dashboard\ProfileDropdown;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $content string */
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

<header class="main-header">

    <?=
        Html::a(
            // '<span class="logo-mini">' . Html::img(Yii::getAlias("@staticWeb/images/logo-square.svg"), ['height' => '35px']) . '</span>'
            '<span class="logo-lg">' . Html::img(Yii::getAlias("@staticWeb/images/logo.png"), ['height' => '35px']) . '</span>',
            Yii::$app->homeUrl,
            ['class' => 'logo hidden-xs']
        )
        ?>

    <nav class="navbar navbar-static-top no-before no-after" role="navigation"
        style="display: flex; align-items: center; justify-content: space-between;">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <?=
            Html::a(
                '<span class="logo-lg visible-xs-inline">' . Html::img(Yii::getAlias("@staticWeb/images/logo.png"), ['height' => '35px']) . '</span>',
                Yii::$app->homeUrl,
                ['class' => 'logo visible-xs-inline hidden']
            )
            ?>
        <?php if (!Yii::$app->getUser()->isGuest) { ?>
            <?=
                LanguageSwitcher::widget([
                    'view' => LanguageSwitcher::VIEW_DROPDOWN
                ])
                ?>
            <div class="user_info_custom_menu">
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li id="menu_container" class="dropdown notifications-menu">
                            <?= AdminNotificationsPanelWidget::widget() ?>
                        </li>
                        <?= ProfileDropdown::widget() ?>

                        <?php if (false) { ?>
                            <li class="">
                                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            </div>
        <?php } ?>
    </nav>
</header>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    <?php ob_start(); ?>
    $("#search_btn").click(() => {
        var searchValue = document.getElementById("searchInput").value;
        var redirectUrl = "<?= Url::to(['location-equipments/location-equipments']) . '?query=' ?>" + searchValue;

        window.location.href = redirectUrl;
    });

    $(document).on('click', '#mark_all_as_read_popup_btn', function () {
        $.ajax({
            url: `<?= Url::to(['/dependency/mark-all-as-read']) ?>`,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response?.response) {
                    $('#menu_container').empty();
                    $('#menu_container').append(response?.response);

                    if (response?.notify == true) {
                        toastr.warning('Success', 'Mark All As Read', {
                            timeOut: 5000,
                            "closeButton": true,
                            "debug": false,
                            "progressBar": true,
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        });
                    }

                    unseen = response?.unseen;
                }
            }
        });
    });

    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>