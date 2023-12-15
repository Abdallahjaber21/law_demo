<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MaintenanceTaskGroup */

$this->title = Yii::t('app', 'Create Maintenance Task Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Maintenance Task Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="maintenance-task-group-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
