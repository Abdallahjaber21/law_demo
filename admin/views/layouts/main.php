<?php

use common\assets\BackendAsset;
use common\assets\BackendRTLAsset;
use common\assets\CustomAdminLteAsset;
use common\components\settings\Setting;
use common\widgets\dashboard\AjaxModal;
use dmstr\helpers\AdminLteHelper;
use dmstr\web\AdminLteAsset;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */


Yii::$container->set(AdminLteAsset::className(), [
    'skin' => Setting::getValue("admin-skin"),
    'css' => null,
]);
CustomAdminLteAsset::register($this);
dmstr\web\AdminLteAsset::register($this);
BackendAsset::register($this);
common\assets\BowerAsset::register($this);
\common\assets\TimePickerAsset::register($this);
if (in_array(Yii::$app->language, Yii::$app->params['rtl-languages'])) {
    BackendRTLAsset::register($this);
}
$this->registerJsFile(Yii::getAlias("@staticWeb/plugins/mousetrap.min.js"));
//for frontend translations
//\lajax\translatemanager\helpers\Language::registerAssets();
//\lajax\translatemanager\widgets\ToggleTranslate::widget();


$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <?php $this->title = "{$this->title} - ".Yii::$app->params['project-name'] ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition sidebar-mini <?= AdminLteHelper::skinClass() ?> fixed">
        <script>
            (function () {
                if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
                    var body = document.getElementsByTagName('body')[0];
                    body.className = body.className + ' sidebar-collapse';
                }
            })();
        </script>
        <?php $this->beginBody() ?>
        <div class="wrapper">

            <?=
            $this->render(
                    'header.php', ['directoryAsset' => $directoryAsset]
            )
            ?>

            <?=
            $this->render(
                    'left.php', ['directoryAsset' => $directoryAsset]
            )
            ?>

            <?=
            $this->render(
                    'content.php', ['content' => $content, 'directoryAsset' => $directoryAsset]
            )
            ?>

        </div>

        <?= AjaxModal::widget() ?>
        <?php //\common\components\notification\widgets\NotificationScripts::widget() ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
