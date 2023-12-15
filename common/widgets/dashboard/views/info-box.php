<?php

use yii\base\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $content string */
/* @var $title string */
/* @var $icon string */
/* @var $color string */
/* @var $number string */
?>

<div class="info-box bg-<?= Html::encode($color) ?>">
    <span class="info-box-icon bg-<?= Html::encode($iconcolor) ?>">
        <i class="fa fa-<?= Html::encode($icon) ?>"></i>
    </span>
    <div class="info-box-content">
        <span class="info-box-text"><?= Html::encode($title) ?></span>
        <span class="info-box-number" style="font-size: <?= Html::encode($size) ?>px;"><?= Html::encode($number) ?></span>
    </div>
</div>
