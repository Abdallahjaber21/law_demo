<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Sector */

$this->title = Yii::t('app', 'Create Sector');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sectors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sector-create">

    <?= $this->render('_form', [
        'model' => $model,
        'main_sector_id' => $main_sector_id,
    ]) ?>

</div>