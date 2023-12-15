<?php

use yii\base\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $content string */
/* @var $title string */
/* @var $icon string */
/* @var $color string */
/* @var $number string */
/* @var $description string */
/* @var $button string */
/* @var $percentage string */
?>

<div class="info-box bg-<?= Html::encode($color) ?>">
    <?php if (!empty($icon)) { ?>
        <span class="info-box-icon"><i class="fa fa-<?= Html::encode($icon) ?>"></i></span>
    <?php } ?>
    <div class="info-box-content <?= empty($icon)?"no-margin":"" ?>">
        <span class="info-box-text"><?= Html::encode($title) ?></span>
        <span class="info-box-number"><?= Html::encode($number) ?></span>

        <div class="progress">
            <div class="progress-bar" style="width: <?= Html::encode($percentage) ?>%"></div>
        </div>
        <span class="progress-description"><?= Html::encode($description) ?></span>
        <?= $button ?>
    </div>
    <!-- /.info-box-content -->
</div>