<?php

use common\components\extensions\Select2;
use common\models\Technician;
use common\widgets\dashboard\PanelBox;
use common\widgets\inputs\LocationPicker;
use yeesoft\multilingual\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TechnicianLocation */
/* @var $form ActiveForm */
?>

<div class="technician-location-form">

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

                <div class="col-md-12">
                    <input type="hidden" name="hidden-address" id="hidden-address"/>
                    <?=
                    LocationPicker::widget([
                        'address_attr' => "hidden-address",
                        'latitude_attr' => Html::getInputId($model, 'latitude'),
                        'longitude_attr' => Html::getInputId($model, 'longitude'),
                        'latitude' => $model->latitude,
                        'longitude' => $model->longitude,
                    ])
                    ?>
                    <br/>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'technician_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Technician::find()->all(), 'id', 'name')
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
                </div>

            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?>    <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

