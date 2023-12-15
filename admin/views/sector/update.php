<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Sector */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Sector',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sectors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="sector-update">

    <?= $this->render('_form', [
        'model' => $model,
        'main_sector_id' => '',

    ]) ?>

</div>