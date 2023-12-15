<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Equipment;
use common\models\EquipmentCa;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use yii\web\JsExpression;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\EquipmentCaValue */
/* @var $form ActiveForm */
?>

<div class="equipment-ca-value-form">
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
                    <?= $form->field($model, 'equipment_id')->widget(
                        Select2::className(),
                        [
                            'options' => ['multiple' => false, 'placeholder' => 'Select an equipment',   'id' => 'equipment-input'],

                            'data' => ArrayHelper::map(Equipment::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                        ]

                    ) ?>
                </div>
                <div class="col-sm-6">
                    <!-- <?= $form->field($model, 'equipment_ca_id')->widget(
                                Select2::className(),
                                [
                                    'options' => ['multiple' => false, 'placeholder' => 'Select an equipment Custom'],

                                    'data' => ArrayHelper::map(EquipmentCa::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                                ]

                            ) ?> -->
                    <?= $form->field($model, 'equipment_ca_id')->widget(DepDrop::className(), [
                        'data'           => ArrayHelper::map(EquipmentCa::find()->all(), 'id', function ($model) {
                            return "{$model->name}";
                        }),
                        'value' => $model->equipment_ca_id,
                        'type'           => DepDrop::TYPE_SELECT2,
                        'options'        => [
                            'placeholder' => Yii::t("frontend", 'Select an Equipment Custom'),
                            'id' => 'country-input'
                        ],
                        'select2Options' => [
                            'theme'         => Select2::THEME_DEFAULT,
                            'pluginOptions' => [
                                "multiple"   => false,
                                'allowClear' => true,
                                'escapeMarkup'      => new JsExpression('function (markup) {return markup; }'),
                                'templateResult'    => new JsExpression('function(res) {return res.text; }'),


                            ]
                        ],
                        'pluginOptions'  => [
                            'depends'     => ["equipment-input"],
                            'initDepends' => [
                                "equipment-input"
                            ],
                            'initialize'  => true,
                            'url'         => Url::to(['/dependency/search-equipment-equipment-ca']),
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
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