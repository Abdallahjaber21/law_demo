<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\RepairRequestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="repair-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'technician_id') ?>

    <?= $form->field($model, 'equipment_id') ?>

    <?= $form->field($model, 'service_type') ?>

    <?= $form->field($model, 'requested_at') ?>

    <?php // echo $form->field($model, 'scheduled_at') 
    ?>

    <?php // echo $form->field($model, 'informed_at') 
    ?>

    <?php // echo $form->field($model, 'arrived_at') 
    ?>

    <?php // echo $form->field($model, 'departed_at') 
    ?>

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

    <?php // echo $form->field($model, 'problem_id') 
    ?>

    <?php // echo $form->field($model, 'assigned_at') 
    ?>

    <?php // echo $form->field($model, 'customer_signature') 
    ?>

    <?php // echo $form->field($model, 'random_token') 
    ?>

    <?php // echo $form->field($model, 'completed_at') 
    ?>

    <?php // echo $form->field($model, 'note') 
    ?>

    <?php // echo $form->field($model, 'technician_signature') 
    ?>

    <?php // echo $form->field($model, 'reported_by_name') 
    ?>

    <?php // echo $form->field($model, 'reported_by_phone') 
    ?>

    <?php // echo $form->field($model, 'notification_id') 
    ?>

    <?php // echo $form->field($model, 'completed_by') 
    ?>

    <?php // echo $form->field($model, 'owner_id') 
    ?>

    <?php // echo $form->field($model, 'team_leader_id') 
    ?>

    <?php // echo $form->field($model, 'description') 
    ?>

    <?php // echo $form->field($model, 'urgent_status') 
    ?>

    <?php // echo $form->field($model, 'division_id') 
    ?>

    <?php // echo $form->field($model, 'project_id') 
    ?>

    <?php // echo $form->field($model, 'location_id') 
    ?>

    <?php // echo $form->field($model, 'category_id') 
    ?>

    <?php // echo $form->field($model, 'repair_request_path') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>