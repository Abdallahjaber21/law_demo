<?php

use common\config\includes\P;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$rules = Yii::$app->authManager->getRules();
$rulesNames = array_keys($rules);
$rulesDatas = array_merge(['' => Yii::t('rbac', '(not use)')], array_combine($rulesNames, $rulesNames));
?>

<div class="auth-item-form">

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php
            $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'lock',
                'color' => PanelBox::COLOR_BLUE
            ]);
            ?>
            <?php
            if (!$model->isNewRecord && P::c(P::ADMINS_ROLE_PAGE_VIEW)) {
                $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->name], [
                    'class' => 'btn btn-danger btn-flat',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                ]);
            }
            ?>
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 1]) ?>

            <!-- <//?= $form->field($model, 'ruleName')->dropDownList($rulesDatas) ?> -->

            <?php if (!Yii::$app->request->isAjax) { ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('rbac', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
                </div>
            <?php } ?>

            <?php ActiveForm::end(); ?>


            <?php PanelBox::end() ?>
        </div>
    </div>
</div>