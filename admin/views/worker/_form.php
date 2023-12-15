<?php

use common\components\extensions\Select2;
use common\widgets\dashboard\PanelBox;
use yeesoft\multilingual\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Worker */
/* @var $form ActiveForm */
?>

<div class="worker-form">

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
                    <?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-12">
                    <label class="control-label" for="worker-workerSectors">Worker sectors</label>
                    <?= Select2::widget([
                        'name'    => 'Worker[workerSectors]',
                        'data'    => \common\models\users\Admin::sectorsKeyValList(),
                        'options' => ['multiple' => true, 'placeholder' => 'Worker sectors ...'],
                        'value'   => ArrayHelper::getColumn($model->getWorkerSectors()->select(['sector_id'])->asArray()->all(), 'sector_id', false)
                    ]) ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>
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

            <?php PanelBox::end() ?>    <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

