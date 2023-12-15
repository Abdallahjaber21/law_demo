<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
// >>>>>> HEAD:admin/views/mall-ppm-tasks-history/_search.php
/* @var $model common\models\search\MallPpmTasksHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mall-ppm-tasks-history-search">
    ========
    /* @var $model common\models\search\ArticleSearch */
    /* @var $form yii\widgets\ActiveForm */
    ?>

    <div class="article-search">
        >>>>>>>> parent of 8474086 (Uat Project):admin/views/article/_search.php

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($model, 'id') ?>

        <<<<<<<< HEAD:admin/views/mall-ppm-tasks-history/_search.php <?= $form->field($model, 'task_id') ?>
            <?= $form->field($model, 'meter_ratio') ?> <?= $form->field($model, 'asset_id') ?>
            <?= $form->field($model, 'ppm_service_id') ?>
            <?php // echo $form->field($model, 'year') 
                                                                                                                                                                                                                                                ?>
            <?php // echo $form->field($model, 'completed_at') 
                                                                                                                                                                                                                                                    ?>
            <?php // echo $form->field($model, 'completed_by') 
                                                                                                                                                                                                                                                        ?>========<?= $form->field($model, 'title') ?>
            <?= $form->field($model, 'subtitle') ?> <?= $form->field($model, 'content') ?>
            <?= $form->field($model, 'image') ?>>>>>>>>> parent of 8474086 (Uat Project):admin/views/article/_search.php

            <?php // echo $form->field($model, 'status') 
            ?>

            <?php // echo $form->field($model, 'created_at') 
            ?>

            <?php // echo $form->field($model, 'updated_at') 
            ?>

            <?php // echo $form->field($model, 'created_by') 
            ?>

            <?php // echo $form->field($model, 'updated_by') 
            ?>

            <?php // echo $form->field($model, 'random_token') 
            ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>

    </div>