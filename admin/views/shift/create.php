<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Shift */

$this->title = 'Create Shift';
$this->params['breadcrumbs'][] = ['label' => 'Shifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
