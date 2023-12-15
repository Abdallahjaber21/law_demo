<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProfessionCategory */

$this->title = 'Create Profession Category';
$this->params['breadcrumbs'][] = ['label' => 'Profession Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profession-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
