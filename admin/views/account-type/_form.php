<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Account;
use common\models\AccountType;
use common\models\Division;
use kartik\switchinput\SwitchInput;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\AccountType */
/* @var $form ActiveForm */

$childs = Account::getAdminHierarchy(true);

?>

<div class="account-type-form">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin(); ?>
            <?php print_r($form->errorSummary($model)); ?>

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                //'icon' => 'plus',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <?php $panel->beginHeaderItem() ?>
            <?= !$model->isNewRecord ? '' : $form->field($model, 'for_backend')->widget(SwitchInput::classname(), [
                'pluginOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                    'onText' => 'True',
                    'offText' => 'False',
                ]
            ]); ?>
            <?php $panel->endHeaderItem() ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'parent_id')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map($childs, 'id', 'label'),
                        'options' => ['placeholder' => 'Select a parent ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
                <?php if (empty(Account::getAdminAccountTypeDivisionModel())) : ?>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'division_id')->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            'options' => ['placeholder' => 'Select a parent ...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]); ?>
                    </div>
                <?php else : ?>
                    <div class="col-sm-6" style="display: none;">
                        <?= $form->field($model, 'division_id')->textInput(['hidden' => true, 'readOnly' => 'true', 'value' => Account::getAdminAccountTypeDivisionModel()->id]); ?>
                    </div>
                <?php endif; ?>

                <div class="col-sm-6">
                    <?php
                    $statusList = $model->status_list;
                    if ($model->status != AccountType::STATUS_DELETED) {
                        unset($statusList[AccountType::STATUS_DELETED]);
                    }
                    $disabled = $model->status === AccountType::STATUS_DELETED ? ['disabled' => true] : []; ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $statusList,
                        'options' => $disabled,
                    ])
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?> <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>