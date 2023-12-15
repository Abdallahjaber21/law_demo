<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Professions */

$this->title = 'Create Professions';
$this->params['breadcrumbs'][] = ['label' => 'Professions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="professions-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
