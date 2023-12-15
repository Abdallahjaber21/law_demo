<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EngineOilTypes */

$this->title = 'Create Engine Oil Types';
$this->params['breadcrumbs'][] = ['label' => 'Engine Oil Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="engine-oil-types-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
