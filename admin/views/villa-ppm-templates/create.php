<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VillaPpmTemplates */

$this->title = 'Create Villa Ppm Templates';
$this->params['breadcrumbs'][] = ['label' => 'Villa Ppm Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="villa-ppm-templates-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
