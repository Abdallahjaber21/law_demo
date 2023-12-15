<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\VillaPpmTemplatesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="villa-ppm-templates-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'sector_id') ?>

    <?= $form->field($model, 'location_id') ?>

    <?= $form->field($model, 'category_id') ?>

    <?php // echo $form->field($model, 'asset_id') ?>

    <?php // echo $form->field($model, 'project_id') ?>

    <?php // echo $form->field($model, 'frequency') ?>

    <?php // echo $form->field($model, 'repeating_condition') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'team_members') ?>

    <?php // echo $form->field($model, 'next_scheduled_date') ?>

    <?php // echo $form->field($model, 'starting_date_time') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
