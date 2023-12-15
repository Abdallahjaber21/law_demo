<?php

use common\components\extensions\Select2;
use common\data\Countries;
use common\models\Location;
use common\widgets\dashboard\PanelBox;
use common\widgets\inputs\ICheck;
use kartik\date\DatePicker;
use yeesoft\multilingual\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form ActiveForm */
?>

<div class="user-form">

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
            <div class="row">
                <div class="col-sm-12">
                    <label class="control-label" for="user-customerusers">User Locations</label>
                    <?= Select2::widget([
                        'name'    => 'User[userLocations]',
                        'data'    => ArrayHelper::map(Location::find()->all(), 'id', 'name'),
                        'options' => ['multiple' => true, 'placeholder' => 'Linked Locations ...'],
                        'value'   => ArrayHelper::getColumn($model->getUserLocations()->select(['location_id'])->asArray()->all(), 'location_id', false)
                    ]) ?>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?=
                    $form->field($model, 'access_type')->widget(Select2::className(), [
                        'data'  => $model->access_type_list,
                        'theme' => Select2::THEME_DEFAULT,
                    ])
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'password_input')->textInput() ?>
                </div>
                <div class="col-sm-6">
                    <?=
                    $form->field($model, 'status')->widget(Select2::className(), [
                        'data'  => $model->status_list,
                        'theme' => Select2::THEME_DEFAULT,
                    ])
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'floor_number')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'birthdate')->widget(DatePicker::className(), [
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format'    => 'yyyy-mm-dd',
                        ]
                    ])
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($model, 'job_category')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'job_title')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'enable_notification')->widget(ICheck::className())->label("") ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'contracts_reminders')->widget(ICheck::className())->label("") ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'maintenance_notifications')->widget(ICheck::className())->label("") ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'news_notifications')->widget(ICheck::className())->label("") ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?php if (empty($model->timezone)) {
                        $model->timezone = "Asia/Dubai";
                    } ?>
                    <?=
                    $form->field($model, 'timezone')->widget(Select2::classname(), [
                        'data'    => Countries::getTimeZonesList(),
                        'theme'   => Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => Yii::t("app", 'Select a time zone ...')
                        ],
                    ])
                    ?>
                </div>
                <div class="col-sm-6">
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?> <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>