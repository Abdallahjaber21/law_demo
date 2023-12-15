<?php

use borales\extensions\phoneInput\PhoneInput;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Account;
use yii\helpers\ArrayHelper;
use common\models\Sector;
use common\models\SegmentPath;
use common\models\Division;
use common\models\Technician;
use common\models\Location;
use common\widgets\inputs\LocationPicker;
use kartik\depdrop\DepDrop;
use pigolab\locationpicker\LocationPickerAsset;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form ActiveForm */

LocationPickerAsset::register($this);


$is_new_record = $model->isNewRecord;

?>

<div class="location-form">

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php $form = ActiveForm::begin(); ?>
            <?php print_r($form->errorSummary($model)); ?>
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                // 'icon' => 'plus',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <?php $panel->beginHeaderItem(); ?>
            <?= $form->languageSwitcher($model); ?>
            <?php $panel->endHeaderItem(); ?>

            <div class="row">
                <!-- Location -->
                <div class="col-sm-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Location'),
                        'icon' => 'map',
                        // 'canMinimize' => true,
                        'color' => PanelBox::COLOR_LIGHTBLUE
                    ]);
                    ?>

                    <?php if (empty(Account::getAdminAccountTypeDivisionModel())): ?>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'division_id')->widget(
                                Select2::className(),
                                [
                                    'options' => ['multiple' => false, 'placeholder' => 'Select a Division', 'id' => 'division-input', 'value' => !empty($model->division_id) ? $model->division_id : Yii::$app->request->get('division_id')],
                                    'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                                ]
                            ) ?>
                        </div>
                    <?php else: ?>
                        <div class="col-sm-6" style="display: none;">
                            <?= $form->field($model, 'division_id')->textInput(['hidden' => true, 'readOnly' => 'true', 'value' => !empty(Yii::$app->request->get('division_id')) ? Yii::$app->request->get('division_id') : Yii::$app->user->identity->division_id]); ?>
                        </div>
                    <?php endif; ?>

                    <div class="col-sm-<?= (empty(Account::getAdminAccountTypeDivisionModel())) ? '4' : '6' ?>">
                        <?php if (empty(Account::getAdminAccountTypeDivisionModel())): ?>
                            <?= $form->field($model, 'sector_id')->widget(DepDrop::className(), [
                                'data' => ArrayHelper::map(Sector::find()->where(['id' => $model->sector_id])->all(), 'id', function ($model) {
                                    return implode(' - ', array_filter([$model->code, $model->name]));
                                }),
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'placeholder' => Yii::t("frontend", 'Select a Sector'),
                                    'id' => 'sector-input',
                                    'value' => !empty(Yii::$app->request->get('sector_id')) ? Yii::$app->request->get('sector_id') : $model->sector_id,
                                ],
                                'select2Options' => [
                                    'theme' => Select2::THEME_DEFAULT,
                                    'pluginOptions' => [
                                        "multiple" => false,
                                        'allowClear' => true,
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression("function(res) { 
                                            
                                            return res.text;  }"),
                                        'templateSelection' => new JsExpression("function (res) { 
                                            // $('#location-segment_path_id').attr('HOLAAAA',res.options);
                                            return res.text; }"),
                                    ]
                                ],
                                'pluginOptions' => [
                                    'depends' => ["division-input"],
                                    'initDepends' => [
                                        "division-input"
                                    ],
                                    'initialize' => false,
                                    'url' => Url::to(['/dependency/search-division-sectors']),
                                    'params' => ['sector-input'],
                                ]
                            ]) ?>
                        <?php else: ?>
                            <?= $form->field($model, 'sector_id')->widget(Select2::className(), [
                                'data' => ArrayHelper::map(Technician::getTechnicianSectorsOptions(), 'id', function ($model) {
                                    return implode(' - ', array_filter([$model->code, $model->name]));
                                }),
                                'options' => [
                                    'placeholder' => Yii::t("frontend", 'Select a Sector'),
                                    'id' => 'sector-input',
                                ],
                            ]); ?>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-<?= (empty(Account::getAdminAccountTypeDivisionModel())) ? '4' : '6' ?>">
                        <?= $form->field($model, 'segment_path_id')->widget(DepDrop::className(), [
                            'data' => ArrayHelper::map(SegmentPath::find()->where(['id' => $model->segment_path_id])->all(), 'id', function ($model) {
                            $segment_value = SegmentPath::getLayersValue($model->value);
                            return "{$model->name}";
                        }),
                            'value' => $model->segment_path_id,
                            'type' => DepDrop::TYPE_SELECT2,
                            'options' => [
                                'placeholder' => Yii::t("frontend", 'Select a structure'),
                                'id' => 'segment_path_input_dropdown'
                            ],
                            'select2Options' => [
                                'theme' => Select2::THEME_DEFAULT,
                                'pluginOptions' => [
                                    "multiple" => false,
                                    'allowClear' => true,
                                    'escapeMarkup' => new JsExpression('function (markup) {return markup; }'),
                                    'templateResult' => new JsExpression("function(res) {
                                        
                                        return res.text; }"),
                                    'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                                ]
                            ],
                            'pluginOptions' => [
                                'depends' => ["sector-input"],
                                'initDepends' => [
                                    "sector-input"
                                ],
                                'initialize' => false,
                                'url' => Url::to(['/dependency/search-sectors-paths']),
                            ]
                        ]) ?>
                    </div>



                    <div class="col-sm-4">
                        <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-4">
                        <?php $statusList = $model->status_list;
                        if ($model->status != Location::STATUS_DELETED) {
                            unset($statusList[Location::STATUS_DELETED]);
                        }
                        $disabled = $model->status === Location::STATUS_DELETED ? ['disabled' => true] : []; ?>
                        <?= $form->field($model, 'status')->widget(Select2::className(), [
                            'data' => $statusList,
                            'options' => $disabled,
                        ])
                            ?>
                    </div>

                    <?php $panel->end(); ?>
                </div>

                <!-- Address -->
                <div class="col-sm-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Address'),
                        'icon' => 'crosshairs',
                        // 'canMinimize' => true,
                        'color' => PanelBox::COLOR_GREEN
                    ]);
                    ?>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'country_id')->textInput(['disabled' => 'disabled', 'readonly' => true]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'state_id')->textInput(['disabled' => 'disabled', 'readonly' => true]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'city_id')->textInput(['disabled' => 'disabled', 'readonly' => true]); ?>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'placeholder' => 'Address']) ?>
                    </div>
                    <div class="col-sm-4 d-none" id="expiry_date">
                        <?= $form->field($model, 'expiry_date')->widget(DateTimePicker::className(), [
                            'pluginOptions' => [
                                'id' => 'my-datetime-picker',
                                'startDate' => date('Y-m-d'),
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii:00'
                            ],
                            'options' => [
                                'id' => 'datetime_picker_input'
                            ]
                        ]);
                        ?>
                    </div>
                    <?php $panel->end(); ?>
                </div>

                <!-- Map -->
                <div class="col-sm-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Geo-Location'),
                        'icon' => 'map',
                        // 'canMinimize' => true,
                        'color' => PanelBox::COLOR_RED
                    ]);
                    ?>
                    <div class="col-sm-12" style="margin-bottom: 2rem;">
                        <?= Html::hiddenInput("map-address", null, ['id' => 'map-address']) ?>
                        <?php if ($model->isNewRecord == 'true') { ?>
                            <?=
                                LocationPicker::widget([
                                    'address_attr' => "map-address",
                                    'latitude_attr' => Html::getInputId($model, 'latitude'),
                                    'longitude_attr' => Html::getInputId($model, 'longitude'),
                                    'latitude' => $model->latitude ? $model->latitude : '25.18880672181399',
                                    'longitude' => $model->longitude ? $model->longitude : '55.252224453863185',
                                ])
                                ?>
                        <?php } else { ?>
                            <?=
                                LocationPicker::widget([
                                    'address_attr' => "map-address",
                                    'latitude_attr' => Html::getInputId($model, 'latitude'),
                                    'longitude_attr' => Html::getInputId($model, 'longitude'),
                                    'latitude' => $model->latitude >= 0 ? $model->latitude : '25.18880672181399',
                                    'longitude' => $model->longitude >= 0 ? $model->longitude : '55.252224453863185',
                                ])
                                ?>
                        <?php } ?>
                        <br />
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
                    </div>
                    <?php $panel->end(); ?>
                </div>

                <!-- Owner -->
                <?php if (empty(Account::getAdminAccountTypeDivisionModel())) { ?>
                    <div
                        class="col-sm-12 ownersection <?php if ($model->division_id !== Location::DIVISION_VILLA) { ?> hidden <?php } ?>">
                        <?php
                        $panel = PanelBox::begin([
                            'title' => Html::encode('Owner/Tenant'),
                            'icon' => 'user',
                            // 'canMinimize' => true,
                            'color' => PanelBox::COLOR_NAVY
                        ]);
                        ?>
                        <div class=" col-sm-6">
                            <?= $form->field($model, 'owner')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6 phone_number_widget_div">
                            <?= $form->field($model, 'owner_phone', )->widget(PhoneInput::className(), [
                                'jsOptions' => [
                                    'preferredCountries' => ['ae'],
                                ]
                            ]) ?>
                        </div>
                        <?php $panel->end(); ?>
                    </div>
                <?php } else {
                    ?>
                    <?php if ((Yii::$app->user->identity->division_id == Location::DIVISION_VILLA)) { ?>
                        <div class="col-sm-12 ownersection ">
                            <?php
                            $panel = PanelBox::begin([
                                'title' => Html::encode('Owner/Tenant'),
                                'icon' => 'user',
                                // 'canMinimize' => true,
                                'color' => PanelBox::COLOR_NAVY
                            ]);
                            ?>
                            <div class=" col-sm-6">
                                <?= $form->field($model, 'owner')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-6 phone_number_widget_div">
                                <?= $form->field($model, 'owner_phone', )->widget(PhoneInput::className(), [
                                    'jsOptions' => [
                                        'preferredCountries' => ['ae'],
                                    ]
                                ]) ?>
                            </div>
                            <?php $panel->end(); ?>
                        </div>
                    <?php }
                } ?>

            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<?= $form->field($model, 'country_id')->hiddenInput(['id' => 'selected-country-input'])->label(false) ?>

<?php echo Html::activeHiddenInput($model, 'city_id'); ?>
<script>
    <?php ob_start(); ?>

    var sector_id = "<?= Yii::$app->request->get('sector_id'); ?>";
    $('#division-input').on('change', function () {
        //  $('.field-sector-input select').val(sector_id).trigger('change');
        var selectedDivision = $('#select2-division-input-container').attr('title');

        var ownerSection = $('.ownersection');
        if (selectedDivision == 'Villa') {
            ownerSection.show();
            ownerSection.removeClass('hidden');

            $("#expiry_date").removeClass('d-none');
        } else {
            ownerSection.hide();
            $('#location-owner').val('');
            $('#location-owner_phone').val('');
        }
    });

    $('#sector-input').on('change', function () {
        var selectedSector = $(this).val();

        if (selectedSector) {
            $.ajax({
                url: "<?= Url::to(['/dependency/get-data-by-sector']) ?>",
                type: "POST",
                dataType: "json",
                data: {
                    sector_id: selectedSector,
                },
                success: function (response) {
                    $("#location-country_id").val(response?.country);
                    $("#location-state_id").val(response?.state);
                    $("#location-city_id").val(response?.city);
                }
            });
        }

    });

    <?php if (!$model->isNewRecord ? 'true' : 'false'): ?>
        $('#division-input').trigger('depdrop:change');
        $('#division-input').trigger('change');

        $('#sector-input').trigger('change');
    <?php endif; ?>

<?php $js = ob_get_clean(); ?> <?php $this->registerJs($js) ?>
</script>