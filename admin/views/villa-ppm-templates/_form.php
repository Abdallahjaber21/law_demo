<?php

use common\models\Category;
use common\models\Division;
use common\models\Equipment;
use common\models\Location;
use common\models\Sector;
use common\models\Technician;
use common\models\VillaPpmTasks;
use kartik\datetime\DateTimePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\VillaPpmTemplates */
/* @var $form ActiveForm */
?>

<div class="villa-ppm-templates-form">
    <input type="hidden" data-url="<?= Url::to(['repair-request/equipment-path']) ?>" id="equipment_path_url">

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
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    $sectors = Division::getSectors(Division::DIVISION_VILLA);
                    ?>
                    <?= $form->field($model, 'sector_id')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map($sectors, 'id', 'name'),
                        'options' => ['placeholder' => 'Select a sector ...', 'id' => 'sector_dropdown'],
                        'pluginOptions' => [
                            'allowClear' => false
                        ],
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'location_id')->widget(DepDrop::className(), [
                        'data' => ArrayHelper::map(Location::find()->andWhere(['<>', 'status', Location::STATUS_DELETED])->where(['id' => $model->location_id])->orderBy(['name' => SORT_ASC])->all(), 'id', function ($model) {
                            return "{$model->code} - {$model->name}";
                        }),
                        'value' => $model->location_id,
                        'type' => DepDrop::TYPE_SELECT2,
                        'options' => [
                            'placeholder' => Yii::t("frontend", 'Select a Location'),
                            'id' => 'location-input-dropdown',
                        ],
                        'select2Options' => [
                            'theme' => Select2::THEME_DEFAULT,
                            'pluginOptions' => [
                                "multiple" => false,
                                'allowClear' => true,
                                'escapeMarkup' => new JsExpression('function (markup) {return markup; }'),
                                'templateResult' => new JsExpression('function(res) {return res.text; }'),
                                'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                            ]
                        ],
                        'pluginOptions' => [
                            'depends' => ['sector_dropdown'],
                            'initDepends' => [
                                "sector_dropdown"
                            ],
                            'initialize' => false,
                            'url' => \yii\helpers\Url::to(['/dependency/search-equipment-locations']),

                        ]
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'category_id')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map(Category::find()->where([
                            '=',
                            'status',
                            Category::STATUS_ENABLED
                        ])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        'options' => ['placeholder' => 'Select a category ...', 'id' => 'category_dropdown_id'],
                        'pluginOptions' => [
                            'allowClear' => false
                        ],
                    ]); ?>

                    <!-- <//?= $form->field($model, 'category_id')->widget(DepDrop::className(), [
                        'data' => ArrayHelper::map(Category::find()->where(['status' => Category::STATUS_ENABLED])->all(), 'id', function ($model) {
                            return "{$model->name}";
                        }),
                        // 'value' => $model->equipment_id,
                        'type' => DepDrop::TYPE_SELECT2,
                        'options' => [
                            'placeholder' => Yii::t("frontend", 'Select a category'),
                            'id' => 'category_dropdown_id',
                        ],
                        'select2Options' => [
                            'theme' => Select2::THEME_DEFAULT,
                            'pluginOptions' => [
                                "multiple" => false,
                                'allowClear' => true,
                                'escapeMarkup' => new JsExpression('function (markup) {return markup; }'),
                                'templateResult' => new JsExpression('function(res) {return res.text; }'),
                                'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                            ]
                        ],
                        'pluginOptions' => [
                            // "location_id_inputbox", 'location-input-dropdown', 
                            'depends' => ['sector_dropdown', 'location-input-dropdown'],
                            'initDepends' => [
                                "sector_dropdown"
                            ],
                            // 'params' => ['location-input-dropdown'],
                            'initialize' => false,
                            'url' => Url::to(['/dependency/get-categories']),
                        ]
                    ]) ?> -->
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'asset_id')->widget(DepDrop::className(), [
                        'data' => ArrayHelper::map(Equipment::find()->where(['id' => $model->asset_id])->all(), 'id', function ($model) {
                            return "{$model->name}";
                        }),
                        // 'value' => $model->equipment_id,
                        'type' => DepDrop::TYPE_SELECT2,
                        'options' => [
                            'placeholder' => Yii::t("frontend", 'Select an equipment'),
                            'id' => 'equipment-input',
                        ],
                        'select2Options' => [
                            'theme' => Select2::THEME_DEFAULT,
                            'pluginOptions' => [
                                "multiple" => false,
                                'allowClear' => true,
                                'escapeMarkup' => new JsExpression('function (markup) {return markup; }'),
                                'templateResult' => new JsExpression('function(res) {return res.text; }'),
                                'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                            ]
                        ],
                        'pluginOptions' => [
                            // "location_id_inputbox", 'location-input-dropdown', 
                            'depends' => ['category_dropdown_id', 'location-input-dropdown'],
                            'initDepends' => [
                                "category_dropdown_id"
                            ],
                            'params' => ['location-input-dropdown'],
                            'initialize' => false,
                            'url' => Url::to(['/dependency/search-location-equipments-text']),
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'project_id')->textInput() ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'frequency')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'repeating_condition')->widget(Select2::classname(), [
                        'data' => $model->repeating_condition_list,
                        'options' => ['placeholder' => 'Select a repeating condition ...'],
                        'pluginOptions' => [
                            'allowClear' => false
                        ],
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'note')->textarea(['maxlength' => true, 'rows' => '6', 'placeholder' => 'Template Note']); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'path')->textarea(['maxlength' => true, 'rows' => '6', 'placeholder' => 'Work Order Path', 'id' => 'repairrequest-repair_request_path']); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'team_members')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map(Technician::find()->where(['division_id' => Division::DIVISION_VILLA, 'status' => Technician::STATUS_ENABLED])->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => 'Select a repeating condition ...',
                            'value' => explode(',', $model->team_members)
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'multiple' => true
                        ],
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'tasks')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map(VillaPpmTasks::find()->where(['status' => Technician::STATUS_ENABLED])->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => 'Select tasks ...',
                            'value' => explode(',', $model->tasks)
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'multiple' => true
                        ],
                    ]); ?>
                </div>
                <!-- <div class="col-sm-6">
                    <//?= $form->field($model, 'next_scheduled_date')->textInput() ?>
                </div> -->
                <div class="col-sm-6">
                    <?= Html::button('Now', ['id' => 'datetime_now_btn', 'class' => 'btn btn-xs btn-warning', 'style' => 'position:absolute; right:15px']); ?>
                    <?= $form->field($model, 'starting_date_time')->widget(DateTimePicker::className(), [
                        'pluginOptions' => [
                            'id' => 'my-datetime-picker',
                            // 'startDate'      => date('Y-m-d'),
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd hh:ii:00'
                        ],
                        'options' => [
                            'id' => 'datetime_picker_input'
                        ]
                    ])
                    ?>
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

            <?php PanelBox::end() ?>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<script>
    <?php ob_start(); ?>
    $("#datetime_now_btn").click(function() {
        //  2023-10-23 14:25:00

        // Get the current datetime in the desired format
        var now = new Date();
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2,
            '0'); // Month is zero-indexed, so add 1 and pad with 0 if needed
        var day = String(now.getDate()).padStart(2, '0');
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var seconds = String(now.getSeconds()).padStart(2, '0');

        var currentDatetime = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;

        // Set the DateTimePicker's value to the current datetime

        $("#datetime_picker_input").val(currentDatetime);
        $("#datetime_picker_input").trigger('change');

    });

    $("#category_dropdown_id").trigger("depdrop:change");

    if ("<?= !$model->isNewRecord ? false : true ?>") { // action Create only

        $('#equipment-input').change(function() {
            let opt_value = $(this).find(":selected").attr("value");

            if (opt_value !== undefined) {
                if (opt_value !== null && opt_value !== '') {
                    // The variable is defined and not empty
                    let url = $('#equipment_path_url').data('url');

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            equipment_id: opt_value,
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            if (response?.response) {
                                $('#repairrequest-repair_request_path')
                                    .val(response?.response);
                            }
                        },
                    });
                }
            }
        });

        $('#sector_dropdown').change(function() {
            let opt_value = $(this).find(":selected").attr("value");

            if (opt_value) {
                // AJAX REQUEST
                let url = $('#equipment_path_url').data('url');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        equipment_id: opt_value,
                        is_location: true
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response?.response) {
                            $('#repairrequest-repair_request_path')
                                .val(response?.response);
                        }
                    },
                });
            } else {
                $('#repairrequest-repair_request_path')
                    .val('');
            }
        });
    }
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>