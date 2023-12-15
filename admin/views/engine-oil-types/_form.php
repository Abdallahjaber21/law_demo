<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\EngineOilTypes */
/* @var $form ActiveForm */
?>

<div class="engine-oil-types-form">

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin(); ?>
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                //'icon' => 'plus',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php $panel->beginHeaderItem() ?>
            <?= $form->languageSwitcher($model); ?>
            <?php $panel->endHeaderItem() ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'oil_viscosity')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'motor_fuel_type_id')->widget(Select2::className(), [
                        'data' => $model->motor_fuel_type_id_list,
                        'options' => [
                            'placeholder' => 'Motor Fuel Type'
                        ]
                    ])
                        ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'can_weight')->textInput() ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'oil_durability')->textInput() ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $model->status_list,
                    ])
                        ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>