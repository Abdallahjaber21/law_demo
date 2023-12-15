<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Account;
use common\models\Category;
use common\models\Division;
use common\models\EquipmentType;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentType */
/* @var $form ActiveForm */
?>

<div class="equipment-type-form">

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
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'category_id')->widget(
                        Select2::className(),
                        [
                            'options' => ['multiple' => false, 'placeholder' => 'Select a Category'],

                            'data' => ArrayHelper::map(Category::find()->where(['status' => Category::STATUS_ENABLED])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                        ]

                    ) ?>

                </div>
                <div class="col-sm-6">
                    <?php
                    $statusList = $model->status_list;
                    if ($model->status != EquipmentType::STATUS_DELETED) {
                        unset($statusList[EquipmentType::STATUS_DELETED]);
                    }
                    $disabled = $model->status === EquipmentType::STATUS_DELETED ? ['disabled' => true] : []; ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $statusList,
                        'options' => $disabled,
                    ])
                        ?>
                </div>
                <?php if (empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)): ?>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'meter_type')->widget(Select2::className(), [
                            'data' => $model->meter_type_list,
                            'options' => ['multiple' => false, 'placeholder' => 'Meter Type',],

                        ])
                            ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'alt_meter_type')->widget(Select2::className(), [
                            'data' => $model->alt_meter_type_list,
                            'options' => ['multiple' => false, 'placeholder' => 'Alt Meter Type',],
                        ])
                            ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'reference_value')->textInput(['maxlength' => true, 'placeholder' => 'Reference Value']) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'equivalance')->textInput(['maxlength' => true, 'placeholder' => 'Equivalance']) ?>
                    </div>
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
<script type="text/javascript">
    <?php ob_start() ?>
    var category_id = "<?= Yii::$app->request->get('category_id'); ?>";
    if (category_id != '') {
        $('.field-equipmenttype-category_id select').val(category_id).trigger('change');
    }
    <?php $js = ob_get_clean() ?> <?php $this->registerJs($js) ?>
</script>