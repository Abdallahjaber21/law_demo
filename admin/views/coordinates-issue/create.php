<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CoordinatesIssue */

$this->title = 'Create Coordinates Issue';
$this->params['breadcrumbs'][] = ['label' => 'Coordinates Issues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coordinates-issue-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
