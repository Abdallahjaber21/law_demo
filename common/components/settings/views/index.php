<?php

use common\components\settings\SettingCategory;
use common\config\includes\P;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $settings_categories SettingCategory[] */

$this->title = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $items = [] ?>
<?php if (!empty($settings_categories)) { ?>
    <?php foreach ($settings_categories as $key => $category) { ?>
        <?php
        $settings = $category->settings;

        if (!empty($settings)) {
            $content = [];
            foreach ($settings as $key => $setting) {
                $content[] = Html::tag("div", $setting->renderInput(), ['class' => 'col-md-4']);
            }
            $items[] = [
                'label' => $category->label,
                'content' => Html::tag("div", implode("", $content), ['class' => 'row']),
            ];
        }
        ?>
    <?php } ?>
<?php } ?>
<?= Html::beginForm('', 'POST', ['enctype' => 'multipart/form-data']) ?>
<div class="nav-tabs-custom clearfix settings-tabs">
    <?=
    Tabs::widget([
        'items' => $items
    ]);
    ?>

    <?php if (P::c(P::ADMINS_SETTINGS_PAGE_UPDATE)) : ?>
        <div class="row">
            <div class="col-sm-2 col-sm-offset-5 col-xs-4 col-xs-offset-4">
                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary btn-flat btn-block']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= Html::endForm() ?>

<script type="text/javascript">
    <?php ob_start() ?>

    // store the currently selected tab in the localstorage
    $(".settings-tabs ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
        var id = $(e.target).attr("href");
        localStorage.setItem("settings-tab", id);
    });

    // on load of the page: switch to the currently selected tab
    var tab = localStorage.getItem("settings-tab");
    $('.settings-tabs a[href="' + tab + '"]').tab('show');

    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>