<?php

use common\components\extensions\Select2;
use common\models\CauseCode;
use common\models\Customer;
use common\models\DamageCode;
use common\models\Manufacturer;
use common\models\ObjectCategory;
use common\models\ObjectCode;
use common\models\RepairRequest;
use common\widgets\dashboard\PanelBox;
use yeesoft\multilingual\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LineItem */
/* @var $form ActiveForm */
?>

<div class="line-item-form">

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
                <?php if ($model->isNewRecord) { ?>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'repair_request_id')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(RepairRequest::find()->all(), 'id', 'id')
                        ]) ?>
                    </div>
                <?php } ?>
                <div class="col-sm-6">
                    <?= $form->field($model, 'object_code_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(ObjectCategory::find()->all(), 'name', function (ObjectCategory $model) {
                            $result = [];
                            foreach ($model->objectCodes as $index => $objectCode) {
                                    $result[$objectCode->id] = "{$model->name} - {$objectCode->name}";
                            }
                            return $result;
                        })
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'cause_code_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(CauseCode::find()->all(), 'id', 'name')
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'damage_code_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(DamageCode::find()->all(), 'id', 'name')
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'manufacturer_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Manufacturer::find()->all(), 'id', 'name')
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?>    <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

