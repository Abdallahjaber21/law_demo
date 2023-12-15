<?php

use yii\base\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $content string */
/* @var $title string */
/* @var $icon string */
/* @var $color string */
/* @var $buttons string */
/* @var $headerItems string */
/* @var $footer string */
/* @var $body boolean */
/* @var $header boolean */
/* @var $solid boolean */
/* @var $withBorder boolean */
/* @var $bgColor string */
/* @var $panelClass string */
/* @var $footerClass string */
/* @var $canMinimize string */
/* @var $help string */
/* @var $canClose boolean */

$cid = Yii::$app->controller->id;
$aid = Yii::$app->controller->action->id;
$mid = Yii::$app->controller->module->id;
$id = "{$cid}-{$aid}-{$mid}-{$id}";
?>
<?php if ($canMinimize) { ?>
    <?php ob_start() ?>
    <button type="button" class="btn btn-box-tool" data-widget="collapse">
        <i class="fa fa-minus"></i>
    </button>
    <?php $headerItems[] = ob_get_clean() ?>
<?php } ?>
<?php if ($canClose) { ?>
    <?php ob_start() ?>
    <button type="button" class="btn btn-box-tool" data-widget="remove">
        <i class="fa fa-times"></i>
    </button>
    <?php $headerItems[] = ob_get_clean() ?>
<?php } ?>
<?php if (!empty($help)) { ?>
    <?php ob_start() ?>
    <button type="button" class="btn btn-box-tool" data-widget="help" data-toggle="tooltip" data-placement="bottom" title="<?= $help ?>">
        <i class="fa fa-question"></i>
    </button>
    <?php $headerItems[] = ob_get_clean() ?>
<?php } ?>
<div id="<?= $id ?>" class="box box-<?= Html::encode($color) ?> <?= $solid ? 'box-solid' : '' ?> <?= $bgColor ? "bg-{$bgColor}" : '' ?> <?= $panelClass ?>">
    <div class="box-header <?= $withBorder ? 'with-border' : '' ?> <?= !$header ? 'hidden' : '' ?>">
        <h3 class="box-title"><i class="fa fa-<?= Html::encode($icon) ?>"></i> <?= $title ?></h3>
        <?php if (!empty($buttons) || !empty($headerItems)) { ?>
            <div class="pull-right box-tools">
                <?php if (!empty($buttons)) { ?>
                    <div class="btn-group">
                        <?= implode("", $buttons) ?>
                    </div>
                <?php } ?>
                <?php if (!empty($headerItems)) { ?>
                    <?= implode("", $headerItems) ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <?php if (!$body) { ?>
        <?= $content ?>
    <?php } else { ?>
        <div class="box-body">
            <?= $content ?>
        </div>
    <?php } ?>
    <?php if (!empty($footer)) { ?>
        <div class="box-footer <?= $footerClass ?>">
            <?= $footer ?>
        </div>
    <?php } ?>

    <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
</div>

<?php if ($canMinimize) { ?>
    <script type="text/javascript">
        <?php ob_start() ?>
        $('#<?= $id ?>').on('expanded.boxwidget', function(e) {
            localStorage.removeItem('<?= $id ?>');
        });
        $('#<?= $id ?>').on('collapsed.boxwidget', function(e) {
            localStorage.setItem('<?= $id ?>', "collapsed");
        });
        if (localStorage.getItem('<?= $id ?>') == "collapsed") {
            $('#<?= $id ?>').boxWidget('collapse')
        }
        <?php $js = ob_get_clean() ?>
        <?php $this->registerJs($js) ?>
    </script>
<?php } ?>
<?php if (!empty($header)) { ?>
    <script>
        <?php ob_start(); ?>
        $('#<?= $id ?>' + ' [data-widget="help"]').tooltip();
        <?php $js = ob_get_clean(); ?>
        <?php $this->registerJs($js); ?>
    </script>
<?php } ?>