<?php

use common\models\users\forms\AbstractLoginForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model AbstractLoginForm */
/* @var $can_resend boolean */
$this->title = Yii::t("app", 'Forgot Password');

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];

?>
<!-- /.login-logo -->
<div class="login-box-body">
    <p class="login-box-msg"><?= Yii::t("app", "Enter the code you received via E-mail") ?></p>

    <?php $form = ActiveForm::begin(['id' => 'forgot-password-form', 'enableClientValidation' => false]); ?>

    <?=
    $form
        ->field($model, 'token', $fieldOptions1)
        ->label(false)
        ->textInput(['placeholder' => $model->getAttributeLabel('token')])
    ?>


    <div class="row">
        <div class="col-xs-6">
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
            <?= Html::submitButton(Yii::t("app", 'Reset Password'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'forgot-password-code-button']) ?>
        </div>
        <!-- /.col -->
    </div>


    <?php ActiveForm::end(); ?>
    <?php if ($can_resend) { ?>
        <div class="text-center">
            <hr/>
            <?= Html::a(Yii::t("app", "Resend Code"), ['/site/resend-code']) ?>
        </div>
    <?php } ?>

</div>
<?=
Html::a(Yii::t("app", "Login"), ['/site/login'], [
    'class' => 'btn btn-primary btn-fill btn-block btn-flat'
])
?>
<!-- /.login-box-body -->