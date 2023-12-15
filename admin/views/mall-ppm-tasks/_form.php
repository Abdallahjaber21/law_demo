<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\EquipmentType;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\MallPpmTasks */
/* @var $form ActiveForm */
?>

<div class="mall-ppm-tasks-form">

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
                <div class="col-sm-12">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Task Name']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'frequency')->widget(Select2::className(), [
                        'data' => $model->frequency_list,
                        'options' => [
                            'placeholder' => 'Frequency List'
                        ]
                    ])
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'equipment_type_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(EquipmentType::find()->orderBy('name')->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => 'Equipment Type List',
                            'multiple' => false
                        ]
                    ])
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'occurence_value')->textInput(['maxlength' => true, 'type' => 'number', 'placeholder' => 'Occurence Value']) ?>
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

            <?php PanelBox::end() ?> <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>