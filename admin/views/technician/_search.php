<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\TechnicianSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="technician-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?php //$form->field($model, 'account_id') 
    ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'password') ?>
    <?= $form->field($model, 'image') ?>


    <?php // echo $form->field($model, 'phone_number') 
    ?>

    <?php // echo $form->field($model, 'address') 
    ?>



    <?php // echo $form->field($model, 'auth_key') 
    ?>

    <?php // echo $form->field($model, 'access_token') 
    ?>

    <?php // echo $form->field($model, 'random_token') 
    ?>

    <?php // echo $form->field($model, 'password_reset_token') 
    ?>

    <?php // echo $form->field($model, 'mobile_registration_id') 
    ?>

    <?php // echo $form->field($model, 'web_registration_id') 
    ?>

    <?php // echo $form->field($model, 'enable_notification') 
    ?>

    <?php // echo $form->field($model, 'locked') 
    ?>

    <?php // echo $form->field($model, 'login_attempts') 
    ?>

    <?php // echo $form->field($model, 'last_login') 
    ?>

    <?php // echo $form->field($model, 'timezone') 
    ?>

    <?php // echo $form->field($model, 'language') 
    ?>

    <?php // echo $form->field($model, 'created_at') 
    ?>

    <?php // echo $form->field($model, 'updated_at') 
    ?>

    <?php // echo $form->field($model, 'status') 
    ?>

    <?php // echo $form->field($model, 'created_by') 
    ?>

    <?php // echo $form->field($model, 'updated_by') 
    ?>

    <?php // echo $form->field($model, 'platform') 
    ?>

    <?php // echo $form->field($model, 'title') 
    ?>

    <?php // echo $form->field($model, 'code') 
    ?>

    <?php // echo $form->field($model, 'profession_id') 
    ?>

    <?php // echo $form->field($model, 'division_id') 
    ?>

    <?php // echo $form->field($model, 'badge_number') 
    ?>

    <?php // echo $form->field($model, 'description') 
    ?>

    <?php // echo $form->field($model, 'longitude') 
    ?>

    <?php // echo $form->field($model, 'latitude') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>