<?php

use common\components\settings\Setting;
use common\models\users\forms\AbstractLoginForm;
use lavrentiev\widgets\toastr\Notification;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model AbstractLoginForm */

$this->title = Yii::t("app", 'Sign In');

$fieldOptions1 = [
  'options' => ['class' => 'form-group has-feedback'],
  'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
  'options' => ['class' => 'form-group has-feedback'],
  'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];


if (!empty($reset_password)) {
  echo Notification::widget([
    'type' => 'success',
    'title' => 'Reset Password',
    'message' => 'Password Reset Successfully!'
  ]);
}
?>
<!-- /.login-logo -->
<div class="login-box-body">
    <?php $lockAttempts = (int)Setting::getValue('max_login_attempts');
  ?>
    <p class="login-box-msg"><?= Yii::t("app", "Sign in to access your dashboard") ?></p>
    <p class="login-box-msg" style="color: #ec4f56; padding:0 0 1.5rem 0;">
        <?= Yii::t("app", "Account will be locked after " . $lockAttempts . " failed attempts.") ?></p>
    <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
    <?=
  $form
    ->field($model, 'email', $fieldOptions1)
    ->label(false)
    ->textInput(['placeholder' => $model->getAttributeLabel('email')])
  ?>

    <?=
  $form
    ->field($model, 'password', $fieldOptions2)
    ->label(false)
    ->passwordInput(['placeholder' => $model->getAttributeLabel('password')])
  ?>

    <div class="row">
        <div class="col-xs-8">
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
            <?= Html::submitButton(Yii::t("app", 'Sign In'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
        </div>
        <!-- /.col -->
    </div>


    <?php ActiveForm::end(); ?>
    <div class="text-center">
        <hr />
        <?= Html::a(Yii::t("app", "Forgot Password"), ['/site/forgot-password']) ?>
    </div>

</div>
<style>
<?php ob_start() ?>.has-error .form-control {
    border: 1px solid #ccc !important
}

<?php $css=ob_get_clean();
?><?php $this->registerCss($css);
?>
</style><!-- /.login-box-body -->