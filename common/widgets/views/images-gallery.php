<?php

use common\assets\plugins\Fancybox3Asset;
use common\models\Image;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $images Image[] */

Fancybox3Asset::register($this);
?>
<div class="clearfix">
    <?php foreach ($images as $key => $image) { ?>

        <?=

        Html::a(Html::tag("div", "", [
            'class' => 'image-thumb',
            'style' => "background-image: url('{$image->getImageFileUrl('image')}');"
        ]) . Html::tag("div", $image->note, ['class' => 'text-center text-black']), $image->getImageFileUrl('image'), [
            'data' => [
                'fancybox' => 'gallery',
                'caption' => Html::encode($image->note)
            ],
            'style' => 'display: inline-block; width:100px;height:100px;'
        ])
        ?>
    <?php } ?>
</div>