<?php

use common\models\LocationEquipments;
use common\models\Sector;
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Division;
use common\models\Equipment;
use common\models\Location;
use common\models\RepairRequest;
use common\models\Technician;
use common\widgets\inputs\assets\ICheckAsset;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\RepairRequest */
/* @var $form ActiveForm */
?>

<input type="hidden" data-url="<?= Url::to(['repair-request/equipment-path']) ?>" id="equipment_path_url">
<input type="hidden" data-url="<?= Url::to(['repair-request/location-division-main-sector']) ?>" id="division_main_sector_url">
<input type="hidden" data-url="<?= Url::to(['repair-request/category-equipment-type']) ?>" id="category_equipment_type_url">

<div class="repair-request-form">
    <div class="row">
        <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]); ?>
        <?php print_r($form->errorSummary($model)); ?>
        <div class="col-md-6 col-md-offset-3">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                //'icon' => 'plus',
                'color' => PanelBox::COLOR_GRAY
            ]);

            ?>
            <?php $panel->beginHeaderItem() ?>
            <h5 id="selected_location_division_sector"></h5>
            <?php $panel->endHeaderItem() ?>
            <div class="row">
                <?php if ($model->isNewRecord) : ?>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'division_id')->widget(
                            Select2::className(),
                            [
                                'options' => ['multiple' => false, 'placeholder' => 'Select a Division', 'id' => 'division-input'],
                                'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            ]
                        ) ?>
                    </div>
                <?php else : ?>
                    <div class="col-sm-12" style="display: none;">
                        <?= $form->field($model, 'division_id')->textInput(['readOnly' => 'true', 'id' => 'division-input', 'value' => $model->division_id]); ?>
                    </div>
                <?php endif; ?>

                <!-- INSIDE A POPUP -->
                <div class="modal fade" id="status_modal" tabindex="-1" role="dialog" aria-labelledby="status_modal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Work Order Status</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php
                                $statuses = $model->status_list;

                                unset($statuses[RepairRequest::STATUS_CANCELLED]);
                                unset($statuses[RepairRequest::STATUS_CHECKED_IN]);
                                unset($statuses[RepairRequest::STATUS_CREATED]);
                                unset($statuses[RepairRequest::STATUS_DRAFT]);
                                unset($statuses[RepairRequest::STATUS_ON_HOLD]);
                                unset($statuses[RepairRequest::STATUS_REQUEST_COMPLETION]);
                                unset($statuses[RepairRequest::STATUS_REQUEST_ANOTHER_TECHNICIAN]);
                                unset($statuses[RepairRequest::STATUS_UNABLE_TO_ACCESS]);

                                // if (!$model->isNewRecord) { // update

                                // $statuses = $model->getNextStatus(true);

                                // $statuses[$model->status] = $model->status_label;
                                // }
                                ?>
                                <?= $form->field($model, 'status')->widget(Select2::className(), [
                                    'data' => $statuses,
                                    'pluginOptions' => [
                                        'disabled' => true
                                    ]
                                ])
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- END -->

                <div class="col-sm-<?= $model->isNewRecord ? '6' : '12' ?>">
                    <?= $form->field($model, 'sector_id')->widget(DepDrop::className(), [
                        'data' => ArrayHelper::map(Sector::find()->andWhere(['<>', 'status', Sector::STATUS_DELETED])->where(['id' => $model->sector_id])->all(), 'id', function ($model) {
                            return "{$model->name}";
                        }),
                        'value' => $model->sector_id,
                        'type' => DepDrop::TYPE_SELECT2,
                        'options' => [
                            'placeholder' => Yii::t("frontend", 'Select a Sector'),
                            'id' => 'sector_dropdown'
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
                            'depends' => ['division-input'],
                            'initDepends' => [
                                "equipment_code_inputbox"
                            ],
                            'initialize' => true,
                            'url' => Url::to(['/dependency/search-division-sectors']),

                        ]
                    ])->label('Sector'); ?>
                </div>

                <div class="col-sm-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Equipment - Location'),
                        'icon' => 'map',
                        'color' => PanelBox::COLOR_BLUE
                    ]);
                    ?>
                    <div class="col-sm-6" style="display: none;">
                        <?= $form->field($model, 'equipment_code')->textInput(['id' => 'equipment_code_inputbox', 'maxlength' => true, 'placeholder' => 'Code', 'disabled' => (!$model->isNewRecord ? true : false)])->label('Equipment Code') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php if ($model->isNewRecord) : ?>
                            <?= $form->field($model, 'location_id')->widget(DepDrop::className(), [
                                'data' => ArrayHelper::map(Location::find()->andWhere(['<>', 'status', Location::STATUS_DELETED])->where(['id' => $model->location_id])->all(), 'id', function ($model) {
                                    return "{$model->name}";
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
                                    'url' => Url::to(['/dependency/search-equipment-locations']),

                                ]
                            ]) ?>
                        <?php else : ?>
                            <?= $form->field($model, 'location_id')->widget(DepDrop::className(), [
                                'data' => ArrayHelper::map(Location::find()->andWhere(['<>', 'status', Location::STATUS_DELETED])->where(['id' => $model->location_id])->all(), 'id', function ($model) {
                                    return "{$model->name}";
                                }),
                                'value' => $model->location_id,
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'placeholder' => Yii::t("frontend", 'Select a Location'),
                                    'id' => 'location-input-dropdown',
                                    'disabled' => true
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
                                    'url' => Url::to(['/dependency/search-equipment-locations']),

                                ]
                            ]) ?>
                            <input type="hidden" name="RepairRequest[location_id]" value="<?= $model->location_id ?>">
                        <?php endif; ?>



                    </div>
                    <div class="col-sm-6" style="display: none;">
                        <?= $form->field($model, 'location_code')->textInput(['id' => 'location_id_inputbox', 'maxlength' => true, 'placeholder' => 'Code'])->label('Location Code') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php if ($model->isNewRecord) : ?>
                            <!-- <//?= $form->field($model, 'category_id')->widget(Select2::className(), [
                                'data' => ArrayHelper::map(Category::find()->all(), 'id', 'name'),
                                'options' => [
                                    'placeholder' => 'Category',
                                    'id' => 'category_dropdown_id',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ])
                            ?> -->

                            <?= $form->field($model, 'category_id')->widget(DepDrop::className(), [
                                'data' => [],
                                'value' => $model->category_id,
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
                                    'depends' => ['sector_dropdown'],
                                    'initDepends' => [
                                        "sector_dropdown"
                                    ],
                                    'initialize' => false,
                                    'url' => Url::to(['/dependency/get-categories']),
                                ]
                            ]) ?>
                        <?php else : ?>
                            <?= $form->field($model, 'category_id')->widget(DepDrop::className(), [
                                'data' => [],
                                'value' => $model->category_id,
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'placeholder' => Yii::t("frontend", 'Select a category'),
                                    'id' => 'category_dropdown_id',
                                    'disabled' => true
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
                                    'url' => Url::to(['/dependency/get-categories']),
                                ]
                            ]) ?>
                            <input type="hidden" name="RepairRequest[category_id]" value="<?= $model->category_id ?>">
                        <?php endif; ?>

                    </div>
                    <div class="col-sm-12">

                        <!-- <//?php if ($model->isNewRecord): ?> -->
                        <?= $form->field($model, 'equipment_id')->widget(DepDrop::className(), [
                            'data' => ArrayHelper::map(Equipment::find()->where(['id' => $model->equipment_id])->all(), 'id', function ($model) {
                                return "{$model->name}";
                            }),
                            'value' => $model->equipment_id,
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
                                'url' => Url::to(['/dependency/search-location-equipments-text', 'order_id' => $model->id]),
                            ]
                        ]) ?>
                        <!-- <//?php else: ?>
                            <//?= $form->field($model, 'equipment_id')->widget(DepDrop::className(), [
                                'data' => ArrayHelper::map(LocationEquipments::find()->where(['id' => $model->equipment_id])->all(), 'id', function ($model) {
                                    return "{$model->name}";
                                }),
                                'value' => $model->equipment_id,
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'placeholder' => Yii::t("frontend", 'Select an equipment'),
                                    'id' => 'equipment-input',
                                    'disabled' => true
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
                                    'depends' => ['category_dropdown_id'],
                                    'initDepends' => [
                                        "category_dropdown_id"
                                    ],
                                    'params' => ['location-input-dropdown'],
                                    'initialize' => false,
                                    'url' => Url::to(['/dependency/search-location-equipments-text']),
                                ]
                            ]) ?>
                            <input type="hidden" name="RepairRequest[equipment_id]" value="<//?= $model->equipment_id ?>">
                        <//?php endif; ?> -->
                    </div>


                    <?php $panel->end(); ?>
                </div>

                <div class="col-sm-6 no-error">
                    <?= $form->field($model, 'repair_request_path')->textarea(['maxlength' => true, 'rows' => '6']) ?>
                </div>

                <div class="col-sm-6">
                    <?php
                    $new_list = $model->getNextServiceType(true);
                    if ($model->isNewRecord) {
                        unset($new_list[RepairRequest::TYPE_PPM]);
                    } else {
                        if ($model->service_type != RepairRequest::TYPE_PPM) {
                            unset($new_list[RepairRequest::TYPE_PPM]);
                        }
                    }
                    ?>
                    <?= $form->field($model, 'service_type')->widget(Select2::className(), [
                        'data' => $new_list,
                        'options' => [
                            'disabled' => (!$model->isNewRecord && $model->service_type == RepairRequest::TYPE_PPM) ? true : false
                        ]
                    ])
                    ?>
                </div>

                <!-- <div class="col-sm-6">
                    <//?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $model->status_list,
                    ])
                    ?>
                </div> -->
                <div class="col-sm-6">
                    <?= $form->field($model, 'problem')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <!-- <//?= $form->field($model, 'reported_by_name')->textInput(['maxlength' => true]) ?> -->
                    <?= $form->field($model, 'reported_by_name')->widget(Select2::className(), [
                        //                        'id'            => 'requestor-input',
                        //                        'name'          => 'requestor',
                        'data' => [$model->reported_by_name => $model->reported_by_name],
                        'pluginOptions' => [
                            'initialize' => false,
                            "multiple" => false,
                            "tags" => true,
                            'createTag' => new JsExpression('function (tag) {return {id: tag.term, text: tag.term, tag: true};}'),
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['dependency/search-team']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) {  return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) {  return markup; }'),
                            'templateResult' => new JsExpression('function(res) { return res.text; }'),
                            'templateSelection' => new JsExpression("function (res) { 
                                if (!window.firstTimeSelection) {
                                    window.firstTimeSelection = true;
                                    return res.text;
                                }

                              $('" . Html::getInputId($model, 'reported_by_name') . "').val(res.name);
                              $('#reported_by_phone_number').val(res.phone);
                              return res.text; }"),
                        ],
                        'options' => [
                            'placeholder' => Yii::t("app", 'Search a name ...'),
                            'id' => 'reported_by_name_dropdown'
                        ],
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'reported_by_phone')->textInput(['maxlength' => true, 'id' => 'reported_by_phone_number']); ?>
                </div>
                <div class="col-sm-12 no-error">
                    <?= $form->field($model, 'service_note')->textarea(['maxlength' => true, 'rows' => '6', 'placeholder' => 'Service Note']); ?>
                </div>
                <div class="col-sm-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Options'),
                        'icon' => 'cogs',
                        'color' => PanelBox::COLOR_ORANGE
                    ]);
                    ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'need_review')->checkbox([
                                'class' => 'icheck'
                                // , 'disabled' => $model->isNewRecord ? false : true
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'urgent_status')->checkbox(['class' => 'icheck']); ?>
                        </div>
                        <div class="col-sm-6" id="hide_if_not_mall" style="display: none;">
                            <?= $form->field($model, 'technician_from_another_division')->checkbox(['class' => 'icheck']); ?>
                        </div>
                    </div>
                    <?php $panel->end(); ?>
                </div>
                <div class="col-sm-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Assignees'),
                        'icon' => 'user',
                        'color' => PanelBox::COLOR_BLUE
                    ]);
                    ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= Html::button('Now', ['id' => 'datetime_now_btn', 'class' => 'btn btn-xs btn-warning', 'style' => 'position:absolute; right:15px']); ?>
                            <?= $form->field($model, 'scheduled_at')->widget(DateTimePicker::className(), [
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
                        <!-- <input type="hidden" id="supervisors_arr" value='<//?= Json::encode(Technician::getSupervisorsTechnicians()); ?>'> -->
                        <div class="col-sm-6">
                            <?= $form->field($model, 'owner_id')->widget(DepDrop::className(), [
                                'data' => null,
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'placeholder' => Yii::t("frontend", 'Select a Supervisor'),
                                    'multiple' => false
                                ],
                                'select2Options' => [
                                    'theme' => Select2::THEME_DEFAULT,
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                                    ]
                                ],
                                'pluginOptions' => [
                                    'depends' => ["category_dropdown_id", "datetime_picker_input"],
                                    'initDepends' => [
                                        "category_dropdown_id"
                                    ],
                                    'initialize' => false,
                                    'url' => Url::to(['/dependency/search-supervisor-technicians', 'type' => 'supervisor']),
                                    'params' => ["location_id_inputbox", "location-input-dropdown"]

                                    // 'params' => ['supervisors_arr', 'selected_location_division_sector']
                                ],
                            ]); ?>
                        </div>
                        <div class="col-sm-8" id="assignees_id_input">
                            <?= $form->field($model, 'technician_id')->widget(DepDrop::className(), [
                                'data' => null,
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'placeholder' => Yii::t("frontend", 'Assign a team'),
                                    'multiple' => true,
                                    'id' => 'technician-id-input'
                                ],
                                'select2Options' => [
                                    'theme' => Select2::THEME_DEFAULT,
                                    'pluginOptions' => [
                                        'allowClear' => false,
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                                    ]
                                ],
                                'pluginOptions' => [
                                    'depends' => ["category_dropdown_id", "datetime_picker_input"],
                                    'initDepends' => [
                                        "category_dropdown_id"
                                    ],
                                    'initialize' => false,
                                    'url' => Url::to(['/dependency/search-supervisor-technicians', 'type' => 'technician', 'order_id' => $model->id]),
                                    'params' => ["location_id_inputbox", "location-input-dropdown"]

                                ],
                            ]); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'team_leader_id')->widget(DepDrop::className(), [
                                'data' => ArrayHelper::map(Technician::find()->where(['id' => $model->technician_id])->all(), 'id', function ($model) {
                                    return $model->name;
                                }),
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'placeholder' => Yii::t("frontend", 'Assign a team leader'),
                                    'multiple' => false,
                                ],

                                'select2Options' => [
                                    'theme' => Select2::THEME_DEFAULT,
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                                    ]
                                ],
                                'pluginOptions' => [
                                    'depends' => ["technician-id-input"],
                                    // 'initDepends' => [
                                    //     "location-input"
                                    // ],
                                    'initialize' => true,
                                    'url' => Url::to(['/dependency/team-leader', 'order_id' => $model->id]),
                                    'params' => ['model_id']
                                ],
                            ])
                                ->label("Team Leader") ?>
                        </div>

                    </div>
                    <?php $panel->end(); ?>
                </div>




            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>
            <?php PanelBox::end() ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>

<?php ICheckAsset::register($this) ?>
<script>
    <?php ob_start(); ?>
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-orange',
        radioClass: 'icheckbox_square-orange',
        increaseArea: '20%'
    });
    $('.s2-select-label', '.s2-unselect-label').click(function() {
            $('#technician-id-input').trigger('depdrop:change');

        }

    );


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
    } else {
        $('#sector_dropdown').trigger('depdrop:change');
    }


    // Fill Division - Main Sector - Sector
    $('#location-input-dropdown').change(function() {
        let opt_value = $(this).find(":selected").attr("value");

        if (opt_value) {
            getDivisionMainSectorSector($(this).val());
        }
    });

    function getDivisionMainSectorSector(location_code) {
        let division_main_sector_url = $('#division_main_sector_url').data('url');

        $.ajax({
            type: 'POST',
            url: division_main_sector_url,
            data: {
                location_id: location_code,
            },
            dataType: 'JSON',
            success: function(response) {
                if (response?.response) {
                    $('#selected_location_division_sector')
                        .text(response?.response);
                    let division_id = response?.division_id;

                    $('#selected_location_division_sector')
                        .val(division_id);

                    if (division_id != <?= Division::DIVISION_MALL ?>) {
                        $('#hide_if_not_mall').css('display', 'none');
                    } else {
                        $('#hide_if_not_mall').css('display', 'block');
                    }
                }
            },
        });
    }

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

    $("#datetime_picker_input").change(function() {
        // Get the selected date value
        var selectedDate = new Date($(this).val());

        // Get today's date
        var today = new Date();
        today.setHours(0, 0, 0, 0); // Set time to midnight for accurate comparison

        // Compare selected date with today's date
        if (selectedDate < today) {
            // If selected date is earlier than today, show the modal
            $("#repairrequest-status").removeAttr('disabled');
            $('#status_modal').modal('show');
        } else {
            $("#repairrequest-status").attr('disabled', 'true');
        }
    });

    <?php $js = ob_get_clean(); ?> <?php $this->registerJs($js); ?>
</script>