<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yeesoft\multilingual\assets\LanguageSwitcherAsset;

/* @var $this yii\web\View */

LanguageSwitcherAsset::register($this);
?>
<div class="navbar-custom-menu pull-left">
    <ul class="nav navbar-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <?= $languages[$language] ?>
                &nbsp;<i class="fa fa-caret-down"></i>
            </a>

            <ul class="dropdown-menu">
                <?php foreach ($languages as $key => $lang) { ?>
                    <?php $title = ($display == 'code') ? $key : $lang; ?>
                    <li>
                        <?= Html::a($title, ArrayHelper::merge($params, [$url, 'language' => $key, 'forceLanguageParam' => true])) ?>
                    </li>
                <?php } ?>
            </ul>
        </li>    
    </ul>
</div>