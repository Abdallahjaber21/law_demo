<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Account;
use common\models\Division;
use common\models\Technician;
use common\models\Equipment;
use common\models\LocationEquipments;
use kartik\switchinput\SwitchInput;
use common\models\Location;
use yii\helpers\ArrayHelper;

$this->title = 'Update Location Equipment: ' . @$model->equipment->name . ' for location: ' . @$model->location->name;
?>
<?php
$location_id = Yii::$app->request->get('location_id');
$location = Location::findOne(Yii::$app->request->get('location_id')); ?>
<div class="location-equipments-form">

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
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <input type="hidden" name="LocationEquipments[equipment_id]" value="<?= $model->equipment_id ?>">
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $model->status_list,
                    ])
                        ?>
                </div>

                <?php if (@$model->location->division_id == Division::DIVISION_PLANT): ?>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'driver_id')->widget(
                            Select2::className(),
                            [
                                'options' => ['multiple' => false, 'placeholder' => 'Select a Driver', 'id' => 'division-input'],
                                'data' => ArrayHelper::map(Technician::find()->where(['status' => Technician::STATUS_ENABLED])->andWhere(['IN', 'id', LocationEquipments::getDriverTechnicianId()])->andWhere(['division_id' => $model->location->division_id])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                            ]
                        ) ?>
                    </div>

                    <?php $is_plant = $model->division_id == Division::DIVISION_PLANT; ?>

                    <div class="col-sm-<?= $is_plant ? "4" : "6" ?>">
                        <?= $form->field($model, 'motor_fuel_type')->widget(Select2::className(), [
                            'data' => $model->motor_fuel_type_list,
                            'options' => [
                                'placeholder' => 'Motor Fuel Type'
                            ]
                        ])
                            ?>
                    </div>

                    <div class="col-sm-<?= $is_plant ? "4" : "6" ?>">
                        <?= $form->field($model, 'chassie_number')->textInput(['maxlength' => true, 'placeholder' => 'chassis Number']) ?>
                    </div>

                    <?php if ($is_plant): ?>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'safety_status')->widget(Select2::className(), [
                                'data' => $model->safety_status_list,
                                'options' => [
                                    'placeholder' => 'Safety Status'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ])
                                ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>




                <?php
                $equipment_path = LocationEquipments::getCrudInputsFromLayers($model->value);
                if (!empty($equipment_path)):
                    ?>
                    <!-- Value -->
                    <div class="col-sm-12">
                        <?php
                        $panel = PanelBox::begin([
                            'title' => Html::encode('Equipment Path'),
                            'icon' => 'gear',
                            'color' => PanelBox::COLOR_GREEN
                        ]);
                        ?>
                        <?php foreach ($equipment_path as $index => $v): ?>
                            <div class="col-sm-6">
                                <?= Html::label($v['layer'], $v['layer'], ['class' => 'control-label']); ?>
                                <?= Html::input('text', "LocationEquipments[value][" . $v['layer'] . "]", $v['value'], ['class' => 'form-control']); ?>
                                <p class="help-block help-block-error"></p>
                            </div>
                        <?php endforeach; ?>

                        <?php
                        $panel->end();
                        ?>
                    </div>
                <?php endif; ?>
                <?php $equipments_custom_attributes = LocationEquipments::getCrudInputsFromLayers(Equipment::getJsonEquipmentCustomAttributes($model->equipment_id, $model->id));
                if (!empty($equipments_custom_attributes)):
                    ?>
                    <div class="col-sm-12">
                        <?php
                        $panel = PanelBox::begin([
                            'title' => Html::encode('Equipment Custom Attributes'),
                            'icon' => 'gear',
                            'color' => PanelBox::COLOR_ORANGE
                        ]);
                        ?>
                        <?php foreach ($equipments_custom_attributes as $index => $v): ?>
                            <div class="col-sm-6">
                                <?php if ($v['layer'] != "Item Owner") { ?>
                                    <?= Html::label($v['layer'], $v['layer'], ['class' => 'control-label']); ?>
                                    <?= Html::input('text', "LocationEquipments[custom_attributes][" . $v['id'] . "]", $v['value'], ['class' => 'form-control']); ?>
                                    <p class="help-block help-block-error"></p>
                                <?php } else { ?>
                                    <?= Html::label($v['layer'], $v['layer'], ['class' => 'control-label']); ?>

                                    <?php
                                    echo Select2::widget([
                                        'name' => "LocationEquipments[custom_attributes][{$v['id']}]",
                                        'data' => [
                                            'Contracting' => 'Contracting',
                                            'Fibrex Plant' => 'Fibrex Plant',
                                            'Rental' => 'Rental',
                                        ],
                                        'options' => [
                                            'placeholder' => 'Select an item ...',
                                            'class' => 'form-control',
                                        ],
                                        'value' => $v['value'],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                        ],
                                    ]);

                                    ?>

                                    <p class="help-block help-block-error"></p>
                                <?php } ?>

                            </div>
                        <?php endforeach; ?>
                        <?php
                        $panel->end();
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)): ?>
                    <?php

                    if (@$model->location->division_id == Division::DIVISION_PLANT): ?>
                        <div class="col-sm-12">
                            <?php
                            $panel = PanelBox::begin([
                                'title' => Html::encode('PPM Parameters'),
                                'icon' => 'gear',
                                'color' => PanelBox::COLOR_BLUE
                            ]);
                            ?>
                            <div class="col-sm-8">
                                <?= $form->field($model, 'meter_value')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-4">
                                <?= $form->field($model, 'meter_damaged')->widget(SwitchInput::classname(), [
                                    'pluginOptions' => [
                                        'size' => 'small',
                                        'onColor' => 'success',
                                        'offColor' => 'danger',
                                        'onText' => 'Operational',
                                        'offText' => 'Damaged',
                                    ]
                                ]); ?>
                            </div>
                            <?php
                            $panel->end();
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>