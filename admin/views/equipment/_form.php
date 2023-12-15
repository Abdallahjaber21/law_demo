<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\components\extensions\Select2;
use common\models\Account;
use common\models\Category;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\SegmentPath;
use common\models\EquipmentCa;
use common\models\EquipmentCaValue;
use common\widgets\dashboard\PanelBox;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */
/* @var $form ActiveForm */
?>

<div class="equipment-form">
    <input id="url_input_hidden" type="hidden" data-url="<?= Url::to(['equipment/ajax-segment']); ?>">
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
                <?php if (empty(Account::getAdminAccountTypeDivisionModel())) : ?>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'division_id')->widget(
                            Select2::className(),
                            [
                                'options' => ['multiple' => false, 'placeholder' => 'Select a Division', 'id' => 'division-input'],
                                'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                            ]
                        ) ?>
                    </div>
                <?php else : ?>
                    <div class="col-sm-6" style="display: none;">
                        <?= $form->field($model, 'division_id')->textInput(['hidden' => true, 'readOnly' => 'true', 'value' => Yii::$app->user->identity->division_id]); ?>
                    </div>
                <?php endif; ?>


                <div class="col-sm-6">
                    <?= $form->field($model, 'category_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Category::find()->where(['status' => Category::STATUS_ENABLED])->orderBy(['name' => SORT_ASC])->all(), 'id',  'name'),
                        'options'        => [
                            'placeholder' => Yii::t("frontend", 'Select a Category'),
                            'id' => 'category-input',
                            'value' => !empty(Yii::$app->request->get('category_id')) ? Yii::$app->request->get('category_id') : $model->category_id,

                        ],
                    ]);  ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'equipment_type_id')->widget(DepDrop::className(), [
                        'data'           => ArrayHelper::map(EquipmentType::find()->where(['status' => EquipmentType::STATUS_ENABLED])->orderBy(['name' => SORT_ASC])->where(['id' => $model->equipment_type_id])->all(), 'id', function ($model) {
                            return "{$model->code} - {$model->name}";
                        }),
                        'value' => $model->equipment_type_id,
                        'type'           => DepDrop::TYPE_SELECT2,
                        'options'        => [
                            'placeholder' => Yii::t("frontend", 'Select an Equipment Type'),
                            'value' => !empty(Yii::$app->request->get('equipment_type_id')) ? Yii::$app->request->get('equipment_type_id') : $model->equipment_type_id,

                        ],
                        'select2Options' => [
                            'theme'         => Select2::THEME_DEFAULT,
                            'pluginOptions' => [
                                "multiple"   => false,
                                'allowClear' => true,
                                'escapeMarkup'      => new JsExpression('function (markup) {return markup; }'),
                                'templateResult'    => new JsExpression('function(res) {return res.text; }'),
                                'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                            ]
                        ],
                        'pluginOptions'  => [
                            'depends'     => ["category-input"],
                            'initDepends' => [
                                "category-input"
                            ],
                            'initialize'  => true,
                            'url'         => Url::to(['/dependency/search-category-type']),
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'readonly' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    $statusList = $model->status_list;
                    if ($model->status != Equipment::STATUS_DELETED) {
                        unset($statusList[Equipment::STATUS_DELETED]);
                    }
                    $disabled = $model->status === Equipment::STATUS_DELETED ? ['disabled' => true] : []; ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $statusList,
                        'options' => $disabled,
                    ])
                    ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php $panel->end() ?>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<script>
    <?php ob_start(); ?>
    $('#category-input').trigger('depdrop:change');
    $('#equipment-equipment_type_id').change(function() {
        let selected = $(this).find(':selected');

        if (selected.val()) {
            $('#equipment-name').val(selected.text());
        } else {
            $('#equipment-name').val('');

        }
    });
    var equipment_type_id = "<?= Yii::$app->request->get('equipment_type_id'); ?>";
    if (equipment_type_id != '') {
        $('.field-equipmenttype-category_id select').val(category_id).trigger('change');
    }
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>