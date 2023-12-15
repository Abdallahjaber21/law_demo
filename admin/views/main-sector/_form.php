<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Division;
use yii\helpers\ArrayHelper;
use common\models\MainSector;
/* @var $this yii\web\View */
/* @var $model common\models\MainSector */
/* @var $form ActiveForm */
?>

<div class="main-sector-form">

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
                    <?= $form->field($model, 'division_id')->widget(
                        Select2::className(),
                        [
                            'options' => ['multiple' => false, 'placeholder' => 'Select a Division'],

                            'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                        ]

                    ) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    $statusList = $model->status_list;
                    if ($model->status != MainSector::STATUS_DELETED) {
                        unset($statusList[MainSector::STATUS_DELETED]);
                    }
                    $disabled = $model->status === MainSector::STATUS_DELETED ? ['disabled' => true] : []; ?>
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