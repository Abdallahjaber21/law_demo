<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Account;
use common\models\Division;
use common\models\Sector;
use common\models\Technician;
use common\models\SegmentPath;
use kartik\depdrop\DepDrop;
use rmrevin\yii\fontawesome\FA;
use wbraganca\dynamicform\DynamicFormAsset;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\SegmentPath */
/* @var $form ActiveForm */

DynamicFormAsset::register($this);

$js = '

jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(item).find("input").attr("value" , "");
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Level: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Level: " + (index + 1))
    });
});

';

$this->registerJs($js);

$js = 'jQuery(".add-item").removeClass("disabled");';

$this->registerJs($js, $this::POS_LOAD);


?>

<div class="segment-path-form">

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin([
                'id' => 'dynamic-form',
            ]); ?>
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
                <div class="col-sm-4">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-4">
                    <?php
                    $statusList = $model->status_list;
                    if ($model->status != SegmentPath::STATUS_DELETED) {
                        unset($statusList[SegmentPath::STATUS_DELETED]);
                    }
                    $disabled = $model->status === SegmentPath::STATUS_DELETED ? ['disabled' => true] : []; ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $statusList,
                        'options' => $disabled,
                    ])
                        ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'sector_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Technician::getTechnicianSectorsOptions(), 'id', function ($model) {

                        if (!empty($model->mainSector->division->name)) {
                            return implode(' - ', array_filter([$model->code, $model->name, @$model->mainSector->division->name]));
                        } else {
                            return implode(' - ', array_filter([$model->code, $model->name,]));
                        }
                    }),
                        'options' => [
                            'placeholder' => Yii::t("frontend", 'Select a Sector'),
                            'multiple' => true
                        ],
                    ]); ?>
                </div>

                <div class="col-sm-12">
                    <!-- <//?= $form->field($model, 'value')->textInput() ?> -->
                    <?php
                    DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item', // required: css class
                        'limit' => 20, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $segment_pathes_model[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'value',
                        ],
                    ]);
                    ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-heading panel-heading-inner d-flex justify-content-between align-center">
                                <div class="panel-title d-flex align-items-center">
                                    <?= FA::i(FA::_BOLT) ?>
                                    <p class="m-0 mx-1">Segment Path</p>
                                </div>
                                <button type="button" id="add_new_dynamic_widget_btn"
                                    class="pull-right disabled add-item btn btn-success"
                                    style="float: none !important;position: absolute;bottom: -3.4rem;right: 1.5rem;"><i
                                        class="fa fa-plus"></i>Add Level</button>
                            </div>
                            <div class="panel-body container-items">
                                <?php foreach ($segment_pathes_model as $index => $modelAddress): ?>
                                    <div class="item panel panel-default">
                                        <div class="panel-heading">
                                            <span class="panel-title-address">Level:
                                                <?= ($index + 1) ?>
                                            </span>
                                            <button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i
                                                    class="fa fa-minus p-0"></i></button>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-body">
                                            <?php
                                            if (!$modelAddress->isNewRecord) {
                                                echo Html::activeHiddenInput($modelAddress, "[{$index}]id");
                                            }
                                            ?>
                                            <?= $form->field($modelAddress, "[{$index}]value")->textInput(['maxlength' => true]) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <?php DynamicFormWidget::end(); ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary submit-btn btn-flat' : 'btn submit-btn  btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<script>
    <?php ob_start(); ?>

    $(document).on('keyup', '.dynamicform_wrapper input', function (index, el) {
        $('#add_new_dynamic_widget_btn').prop('disabled', false);
    });
    $(document).on('click', '.remove-item', function () {
        $('#add_new_dynamic_widget_btn').prop('disabled', false);

    });
    $('.submit-btn').click(function (e) {
        e.preventDefault();
        let values = [];
        let repeated = false;

        $('.dynamicform_wrapper input').each(function (index, el) {
            let value = $(el).val().trim().toLowerCase();
            let idx = values.findIndex((i) => i == value);

            if (idx >= 0) {
                alert('Some Fields Are duplicated!!');
                $('#add_new_dynamic_widget_btn').prop('disabled', true);
                // $('.container-items input').val('');
                repeated = true;
            } else {
                values.push(value);
            }
        });
        if (!repeated) {
            let uniqueValues = [...new Set(values)];
            if (uniqueValues.length === values.length) {
                $('#dynamic-form').submit();
            }
        }
    });


    $('#add_new_dynamic_widget_btn').click(function (e) {
        let values = [];
        let repeated = false;

        $('.dynamicform_wrapper input').each(function (index, el) {
            let value = $(el).val().trim().toLowerCase();

            let idx = values.findIndex((i) => i == value);

            if (idx >= 0) {
                alert('Some Fields Are duplicated!!');
                $('#add_new_dynamic_widget_btn').prop('disabled', true);
                // $('.container-items input').val('');
                return false;
                e.preventDefault();
            } else {
                values.push(value);
            }
        });

    });
<?php $js = ob_get_clean(); ?> <?php $this->registerJs($js); ?>
</script>