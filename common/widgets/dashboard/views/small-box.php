<?php

use yii\base\View;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this View */
/* @var $content string */
/* @var $title string */
/* @var $icon string */
/* @var $color string */
/* @var $number string */
/* @var $linkLabel string */
/* @var $link string */
?>

<div class="small-box bg-<?= Html::encode($color) ?>">
    <div class="inner">
        <h3><?= Html::encode($number) ?></h3>

        <p><?= Html::encode($title) ?></p>
    </div>
    <div class="icon">
        <i class="fa fa-<?= Html::encode($icon) ?>"></i>
    </div>
    <a href="<?= Url::to($link) ?>" class="small-box-footer">
        <?= Html::encode($linkLabel) ?> <i class="fa fa-arrow-circle-right"></i>
    </a>
</div>