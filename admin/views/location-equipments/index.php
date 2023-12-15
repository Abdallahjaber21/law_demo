<?php

use common\components\extensions\OptionsColumn;
use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\models\Account;
use common\models\Admin;
use common\models\Category;
use common\models\City;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentCa;
use common\models\EquipmentType;
use common\models\Location;
use kartik\depdrop\DepDrop;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\UserAudit;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\LocationEquipments;
use common\models\Technician;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LocationEquipmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Location Equipments For: ' . Location::findOne(Yii::$app->request->get('location_id'))->name;

if (isset($location_id) && $location_id) {
    $this->params['breadcrumbs'][] = ['label' => 'Index', 'url' => ['location/index']];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-equipments-index">
    <div class="row">
        <?php
        $form = ActiveForm::begin([
            'action' => Url::to(['location-equipments/create-equipment'])
        ]); ?>
        <div id="location_equipments_table" class="col-sm-12">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode('Add Equipments'),
                'icon' => 'plus',
                'canMinimize' => true,
                'color' => PanelBox::COLOR_RED,
                'panelClass' => 'Collapsible_flex_panel'
            ]);

            $panel->beginHeaderItem();

            ?>
            <div class="row location_equipments_grid_view">
                <div class="col-sm-12">
                    <div class="form-group">
                        <?= Select2::widget([
                            'name' => 'equipment_category',
                            'value' => '',
                            'data' => ArrayHelper::map(Category::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            'options' => ['multiple' => false, 'placeholder' => 'Select category ...', 'id' => 'equipment_category_input'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]); ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <?= DepDrop::widget([
                        'name' => 'equipment_type',
                        'value' => '',
                        'type' => DepDrop::TYPE_SELECT2,
                        'options' => [
                            'placeholder' => Yii::t("frontend", 'Select a type'),
                            'id' => 'equipment_type_input'
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
                            'depends' => ["equipment_category_input"],
                            'initDepends' => [
                                "equipment_category_input"
                            ],
                            'initialize' => false,
                            'url' => Url::to(['/dependency/search-equipment-types']),
                            'params' => ['city_id'],
                        ]
                    ]); ?>
                </div>
                <!-- <div class="col-sm-4">
                    <?php // Html::input('text', 'equipment_name_input_search', null, ['class' => 'form-control', 'id' => 'equipment_name_input_search', 'placeholder' => 'Equipment Name']) 
                    ?>
                </div> -->
            </div>
            <?php
            $panel->endHeaderItem();
            $location = Location::findOne($location_id);
            $division = $location->division;
            $equipments = $division->getEquipments()->all();

            $newdataProvider = new ArrayDataProvider([
                'allModels' => $equipments,
                'pagination' => false,
            ]);

            echo GridView::widget([
                'dataProvider' => $newdataProvider,
                'filterModel' => null,
                'headerRowOptions' => ['class' => 'location_equipments_header_row'],
                'layout' => '{items} {pager}',
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'headerOptions' => ['class' => 'checkbox-header'],
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return ['id' => 'check-' . $model->id, 'name' => 'Hiii[' . $model->id . ']', 'class' => 'checkbox-header', 'value' => $model->id];
                        },
                    ],
                    'id',
                    [
                        'attribute' => 'name',
                        'value' => function ($model) {
                            return !empty($model->code) ? $model->code . ' - ' . $model->name : $model->name;
                        },
                        'contentOptions' => ['class' => 'equipment_name_column'],
                    ],
                    [
                        'attribute' => 'category',
                        'value' => function ($model) {
                            return "{$model->category->name}";
                        },
                        'contentOptions' => ['class' => 'equipment_category_column'],
                    ],
                    [
                        'attribute' => 'equipment_type',
                        'value' => function ($model) {
                            return "{$model->equipmentType->name}";
                        },
                        'contentOptions' => ['class' => 'equipment_type_column'],
                    ],
                    [
                        'attribute' => 'quantity',
                        'value' => function ($model) {
                            return Html::input('number', 'location_equipment_quantity', null, [
                                'id' => $model->id,
                                'placeholder' => 'quantity',
                                'class' => 'form-control location_equipment_quantity',
                                'max' => 99,
                                'disabled' => 'disabled'
                            ]);
                        },
                        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'details',
                        'value' => function ($model) use ($location_id) {
                            echo $this->render('../location/_equipments_modal', [
                                'model' => $model,
                                'location_id' => $location_id
                            ]);

                            return Html::button(FA::i(FA::_PLUS) . ' Infos', [
                                'class' => 'btn btn-sm btn-success add_info_button_modal', 'data-toggle' => 'modal',
                                'data-target' => '#modal-' . $model->id,
                                'id' => '#button-' . $model->id,
                                'disabled' => true,
                                'data-qty' => $model->id
                            ]);
                        },
                        'format' => 'raw'
                    ],
                ],
            ]);
            ?>

            <?php $panel->end(); ?>
        </div>
        <?php $form->end(); ?>

        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => "Existing Equipments",
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>


            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function ($model) {
                            return Html::a($model->id, Url::to(['view', 'id' => $model->id]));
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'division_id',
                        'value' => function ($model) {
                            return @$model->division->name;
                        },
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'location_id',
                        'value' => function ($model) {
                            return @$model->location->name;
                        },
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'equipment_name',
                        'label' => 'Equipment',
                        'value' => function ($model) {
                            return @$model->equipment->name;
                        },
                    ],
                    [
                        'attribute' => 'driver_id',
                        'visible' => ($location->division_id === Division::DIVISION_PLANT),
                        'value' => function ($model) {
                            return @$model->driver->name;
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'attribute' => 'driver_id',

                            'data' => ArrayHelper::map(Technician::find()->andWhere(['IN', 'id', LocationEquipments::getDriverTechnicianId()])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            'pluginOptions' => [
                                'multiple' => false,
                                'allowClear' => true
                            ],
                            'options' => [
                                'placeholder' => ''
                            ],
                        ])
                    ],
                    'code',

                    [

                        'attribute' => 'meter_value',
                        'value' => function ($model) {
                            return $model->meter_value;
                        },
                        'visible' => $location->division_id === Division::DIVISION_PLANT
                    ],
                    [
                        'attribute' => 'meter_damaged',
                        'value' => function ($model) {
                            // $color = ($model->meter_damaged == 1) ? '#28a745;' : '#dc3545';
                            // return Html::tag('div',  ' ', ['style' => 'width:100%;height:20px;background-color:' . $color . '']);
                            if ($model->meter_damaged === 0 || $model->meter_damaged === 1) {
                                $color = ($model->meter_damaged == 1) ? '#28a745;' : '#dc3545';
                                return Html::tag('div', ' ', ['style' => 'width:100%;height:20px;background-color:' . $color . '']);
                            } else {
                                return null;
                            }
                        },
                        'format' => 'raw',
                        'filter' => Select2::widget([
                            'name' => 'meter_damaged',
                            'attribute' => 'meter_damaged',
                            'data' => [
                                '1' => 'Operational',
                                '0' => 'Damaged',
                            ],
                            'value' => Yii::$app->request->get('meter_damaged'),
                            'options' => [
                                'placeholder' => Yii::t("app", 'Meter Damaged'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]),
                        'visible' => $location->division_id === Division::DIVISION_PLANT
                    ],
                    [
                        'attribute' => 'motor_fuel_type',
                        'class' => OptionsColumn::class,
                        'visible' => $location->division_id === Division::DIVISION_PLANT
                    ],
                    [
                        'attribute' => 'chassie_number',
                        'value' => function ($model) {
                            return $model->chassie_number;
                        },
                        'visible' => $location->division_id === Division::DIVISION_PLANT
                    ],
                    [

                        'attribute' => 'safety_status',
                        'class' => common\components\extensions\OptionsColumn::class,
                        'visible' => $location->division_id === Division::DIVISION_PLANT,
                    ],
                    [
                        'attribute' => 'value',
                        'value' => function ($model) {
                            return Equipment::getLayersValue($model->value);
                        },
                        'format' => 'raw',
                        'filter' => false
                    ],
                    [
                        'attribute' => 'custom_attributes',
                        'label' => 'Attributes',
                        'visible' => EquipmentCa::getEquipmentCustomAttributeDivisionCount(@$location->division_id) > 0,
                        'value' => function ($model) {
                            return Equipment::getEquipmentCustomAttributes($model->equipment_id, $model->id);
                        },
                        'format' => 'raw',
                        'filter' => false
                    ],
                    // 'value:ntext',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'class' => common\components\extensions\DateColumn::class,

                    ],

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete} {audit}',
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                if ($model->status == LocationEquipments::STATUS_DELETED) {
                                    return Html::a('Undelete', $url, [
                                        'title' => Yii::t('yii', 'Undelete'),
                                        'class' => 'btn btn-xs btn-warning',
                                        'style' => 'min-width:53px;',
                                        'data-method' => 'post',
                                        'data-confirm' => 'Are you sure you want to undelete this item?',
                                    ]);
                                } else {
                                    $confirmMessage = 'Are you sure you want to delete this item?';
                                    return Html::a('Delete', $url, [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'class' => 'btn btn-xs btn-danger',
                                        'style' => 'min-width:53px;',
                                        'data-method' => 'post',
                                        'data-confirm' => $confirmMessage,
                                    ]);
                                }
                            },

                        ],
                        'permissions' => [
                            'audit' => P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_AUDIT,
                            'delete' => P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_DELETE,
                        ],

                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<style>
    .grid-view tr td:last-child {
        display: flex;
        justify-content: space-between !important;
    }

    .grid-view tr td:last-child a {
        margin-right: 2px;
    }
</style>
<script>
    <?php ob_start(); ?>

    <?php $equipments_occurence_count = is_array($location_equipments) ? array_count_values(ArrayHelper::getColumn($location_equipments, 'equipment_id')) : null; ?>
    <?php $equipments_attrs_occurence_count = is_array($equipment_custom_attributes) > 0 ? array_count_values(ArrayHelper::getColumn($equipment_custom_attributes, 'equipment_id')) : null; ?>

    var array_path =
        <?= !empty(Location::findOne($location_id)->segmentPath) ? Location::findOne($location_id)->segmentPath->value : [] ?>;
    var equipments_attributes = <?= Json::encode($equipment_custom_attributes) ?>;


    var existing_arr = <?= Json::encode($location_equipments) ?>;
    var equipment_count = <?= Json::encode($equipments_occurence_count) ?>;
    var equipment_attrs_count = <?= Json::encode($equipments_attrs_occurence_count) ?>;

    $('input[type="checkbox"]').on('click', function () {
        updateButtons();
    });

    $('.select-on-check-all').on('click', function () {
        var isChecked = $(this).prop('checked');
        $('input[type="checkbox"]').prop('checked', isChecked);
        updateButtons();
    });

    function updateButtons() {
        $('input[type="checkbox"]').each(function () {
            var button = $(this).closest('tr').find('button');
            var qty = $(this).closest('tr').find('.location_equipment_quantity');
            var isChecked = $(this).prop('checked');
            if (qty.val() > 0) {
                button.prop('disabled', !isChecked);
            } else {
                button.prop('disabled', isChecked);
            }
            qty.prop('disabled', !isChecked);
        });
    }


    $('.add_info_button_modal').click(function (event) {

        let elm = $(this);

        let qty_elm = $('#' + elm.data('qty'));

        let enteredValue = qty_elm.val();
        let id = qty_elm.attr('id');

        if (enteredValue) {
            if (!isNaN(enteredValue) && enteredValue >= 0 && enteredValue <= 999) { } else {
                qty_elm.val(qty_elm.val().slice(0, 1));
            }

            let existing_quantity = equipment_count[elm.data('qty')];
            let quantity = enteredValue;

            if (existing_quantity) {
                if ((enteredValue > existing_quantity)) {
                    quantity = enteredValue - existing_quantity;
                } else if (enteredValue == existing_quantity) {
                    alert('Increment Only!!');
                    event.preventDefault();
                    event.stopPropagation();
                    return;
                } else {
                    alert('Increment Only!!');
                    qty_elm.val(existing_quantity);
                    event.preventDefault();
                    event.stopPropagation();
                    return;
                }
            }

            $('#modal-' + id + ' .row').empty();

            let html_el = '';
            for (let index = 0; index < quantity; index++) {

                html_el += '<div class="row dynamic_modal_row" style="margin:0;">';

                html_el +=
                    '<div class="form-group dynamic_modal_column_code field-location-equipment-codes field-location-equipment-code-' +
                    index + '">' +
                    '<label class="control-label" for="location-equipment-code-' + index + '">Code</label>' +
                    '<input type="text"autocomplete="off" id="location-equipment-code-' + id + '-' + index +
                    '" placeholder="Asset Number" class="form-control" name="LocationEquipments[' + id + '][' +
                    index + '][code]" maxlength="255">' +
                    '<p class="help-block help-block-error"></p>' +
                    '</div>';

                html_el += '<div class="dynamic_path_row">';

                for (let index2 = 0; index2 < array_path.length; index2++) {
                    let concatinated_layer = concatenateWords(array_path[index2]['layer']);
                    html_el += '<div class="form-group dynamic_modal_column field-location-equipment-' +
                        concatinated_layer + ' ">' +
                        '<label class="control-label" for="location-equipment-' + concatinated_layer + '-' +
                        array_path[index2]['id'] + '">' + array_path[index2]['layer'] + '</label>' +
                        '<input type="text"autocomplete="off" id="location-equipment-' + concatinated_layer + '-' +
                        id + '-' + index +
                        '" placeholder="' + array_path[index2]['layer'] +
                        '" class="form-control" name="LocationEquipments[' + id + '][' + index + '][value][' +
                        concatinated_layer + ']"  maxlength="255">' +
                        '<div class="layervalues_results"></div>' +
                        '<p class="help-block help-block-error"></p>' +
                        '</div>';
                }

                html_el += '</div>';

                html_el += '<div class="dynamic_attributes_row">';

                for (let index3 = 0; index3 < equipments_attributes.length; index3++) {
                    let concatinated_layer = concatenateWords(equipments_attributes[index3]['name']);
                    html_el += '<div class="form-group dynamic_modal_att_column field-location-equipment-' +
                        concatinated_layer + ' ">' +
                        '<label class="control-label" for="location-equipment-' + concatinated_layer + '-' +
                        equipments_attributes[index3]['id'] + '">' + equipments_attributes[index3]['name'] +
                        '</label>' +
                        '<input type="text"autocomplete="off" id="location-equipment-' + concatinated_layer + '-' +
                        id + '-' + index +
                        '" placeholder="' + equipments_attributes[index3]['name'] +
                        '" class="form-control" name="LocationEquipments[' + id + '][' + index + '][Ca_value][' +
                        equipments_attributes[index3]['id'] + ']" data-id="' + equipments_attributes[index3]['id'] +
                        '" maxlength="255">' +
                        '<div class="cavalues_results"></div>' +
                        '<p class="help-block help-block-error"></p>' +
                        '</div>';
                }

                html_el += '</div>';

                html_el += '</div>';
            }

            $('#modal-' + id + ' .row').append(html_el);

        } else {
            $(this).prop('disabled', true);
            event.preventDefault();
        }

    });
    $('.location_equipment_quantity').on('keyup', function () {
        var button = $(this).closest('tr').find('button');
        var isChecked = $(this).prop('checked');
        if ($(this).val() <= 0) {
            button.prop('disabled', !isChecked);
        } else {
            button.prop('disabled', isChecked);
        }
    });
    $('.location_equipment_quantity').change(function () {

        let element = $(this);

        let enteredValue = parseInt(element.val());
        let id = element.attr('id');

        $('#button-' + id).prop('disabled', false);

        if (enteredValue) {

            if (!isNaN(enteredValue) && enteredValue >= 0 && enteredValue <= 99) { } else {
                element.val(element.val().slice(0, 1));
            }
        }

    });
<?php $js = ob_get_clean(); ?> <?php $this->registerJs($js) ?>
</script>

<script>
    <?php ob_start(); ?>
    $('#equipment_category_input').change(function () {
        let selected_value = ($(this).find(':selected').text()).trim().toLowerCase();
        let selected_value_value = ($(this).find(':selected').val());

        FilterBy(selected_value, selected_value_value, '.equipment_category_column');
    });

    $('#equipment_type_input').change(function () {
        let selected_value = ($(this).find(':selected').text()).trim().toLowerCase();
        let selected_value_value = ($(this).find(':selected').val());

        if (selected_value_value) {
            FilterBy(selected_value, selected_value_value, '.equipment_type_column');
        } else {
            $('#equipment_category_input').trigger('change');
        }
    });

    $('#equipment_name_input_search').keyup(function () {
        let selected_value = $(this).val();

        FilterByLike(selected_value, '.equipment_name_column');
    });

    function FilterBy(selected_value, selected_value_value, filter_query) {
        if (selected_value_value) {
            $('#location_equipments_table tr:not(.location_equipments_header_row)').each(function (index, el) {
                let el_val = ($(el).find(filter_query).text()).trim().toLowerCase();

                if (el_val != selected_value) {
                    $(el).addClass('hidden');
                } else {
                    $(el).removeClass('hidden');
                }
            });
        } else {
            RemoveFilter();
        }
    }

    function FilterByLike(selected_value, filter_query) {
        if (selected_value) {
            $('#location_equipments_table tr:not(.location_equipments_header_row)').each(function (index, el) {
                let el_val = ($(el).find(filter_query).text()).trim().toLowerCase();

                if (!el_val.includes(selected_value.toLowerCase())) {
                    $(el).addClass('hidden');
                } else {
                    $(el).removeClass('hidden');
                }
            });
        } else {
            RemoveFilter();

            $('#equipment_category_input').trigger('change');

        }
    }

    function RemoveFilter() {
        $('#location_equipments_table tr:not(.location_equipments_header_row)').each(function (index, el) {
            $(el).removeClass('hidden');
        })
    };

    function concatenateWords(inputString) {
        // Split the input string into an array of words
        const words = inputString.split(' ');

        // Join the words with underscores
        const result = words.join('_');

        return result.toLowerCase();
    }
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>

<script>
    <?php ob_start(); ?>

    $(document).on('keypress', '.location_equipment_quantity', function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });

    for (let index = 0; index < existing_arr.length; index++) {

        let equipment_id = existing_arr[index]['equipment_id'];
        // Auto Check Selection
        $('#check-' + equipment_id).prop('checked', true);
        updateButtons();
        // Auto Fill Qty Section
        if (equipment_count.hasOwnProperty(equipment_id)) {
            // If it exists, set the qty input value to the corresponding count
            $('#segment_path_input_dropdown').trigger('change');

            $('#' + equipment_id).val(equipment_count[equipment_id]).trigger('keyup');
        }
    }

    // Fill modal inputs values
    let count = 0;

    $.each(equipment_count, function (key, value) {
        let equipment_id = key;

        for (var i = 0; i < value; i++) { // 0 , 1 , 2 , 0 for 3
            let element = existing_arr[count];
            let equipment_code = element['code'];
            let equipment_value = JSON.parse(element['value']);
            let custom_attributes = element['custom_attributes'];

            $('#location-equipment-code-' + equipment_id + '-' + i).val(equipment_code);

            $.each(equipment_value, function (key, value) {
                $(`#location-equipment-${value?.layer}-${equipment_id}-${i}`).val(value?.value);
            });

            for (let index = 0; index < custom_attributes.length; index++) {
                const element = custom_attributes[index];
                $(`#location-equipment-${concatenateWords(custom_attributes[index].name)}-${equipment_id}-${count}`)
                    .val(custom_attributes[index].value);
            }

            count++;
        }
    });

    var layerValues = {};
    var selectedInput = null;

    function fetchLayerValues(layer, callback) {
        var location_id = "<?php echo $location_id; ?>";
        if (!layerValues[layer]) {
            $.ajax({
                url: "<?= Url::to(['/location-equipments/get-layer-values']) ?>",
                dataType: 'json',
                data: {
                    layer: layer,
                    location_id: location_id,
                },
                success: function (data) {
                    layerValues[layer] = data;
                    callback(layer);
                }
            });
        } else {
            callback(layer);
        }
    }


    function displayLayerValuesResults(input) {
        var searchText = input.val().toLowerCase();
        var idParts = input.attr('id').split('-');

        console.warn('<<< idParts >>>', idParts);

        if (idParts.length >= 3 && idParts[0] === 'location' && idParts[1] === 'equipment') {
            var layer = idParts[2];
            if (layerValues[layer]) {
                var inputValues = new Set();
                $('.dynamic_path_row input[id^="location-equipment-' + layer + '-"]').not(input).each(function () {
                    inputValues.add($(this).val().toLowerCase());
                });
                inputValues = Array.from(inputValues).concat(layerValues[layer]);
                var filteredValues = inputValues.filter(function (value) {
                    return value.includes(searchText);
                }).sort();
                var layerValuesResults = input.parent().find('.layervalues_results');
                layerValuesResults.empty();
                if (filteredValues.length > 0) {
                    filteredValues.forEach(function (result) {
                        var valueDiv = $('<div>' + result + '</div>');
                        layerValuesResults.append(valueDiv);
                        valueDiv.on('click', function () {
                            input.val(result);
                            layerValuesResults.empty();
                        });
                    });
                }
            }
        }
    }

    $(document).on('focus', '.dynamic_path_row input[id^="location-equipment-"]', function () {
        if (selectedInput && selectedInput !== this) {
            var otherAutocompleteResults = $(selectedInput).parent().find('.layervalues_results');
            otherAutocompleteResults.empty();
        }
        selectedInput = $(this);

        console.warn('<<< selectedInput >>>', selectedInput);

        fetchLayerValues(selectedInput.attr('id').split('-')[2], function (layer) {
            displayLayerValuesResults(selectedInput);
        });
    });

    $(document).on('input', '.dynamic_path_row input[id^="location-equipment-"]', function () {
        displayLayerValuesResults($(this));
    });



    var CaValues = {};
    var selectedInput = null;

    function fetchCaValues(id, callback) {
        var location_id = "<?php echo $location_id; ?>";
        if (!CaValues[id]) {
            $.ajax({
                url: "<?= Url::to(['/location-equipments/get-ca-values']) ?>",
                dataType: 'json',
                data: {
                    id: id,
                    location_id: location_id
                },
                success: function (data) {
                    CaValues[id] = data;
                    callback(id);
                }
            });
        } else {
            callback(id);
        }
    }

    function displayCaValuesResults(input) {
        var searchText = input.val().toLowerCase();
        var id = input.data('id');
        if (CaValues[id]) {
            var inputValues = new Set();
            $('.dynamic_attributes_row input[data-id^="' + id + '"]').not(input).each(function () {
                inputValues.add($(this).val().toLowerCase());
            });
            inputValues = Array.from(inputValues).concat(CaValues[id]);
            inputValues.sort();
            var CaValuesResults = input.parent().find('.cavalues_results');
            CaValuesResults.empty();
            if (inputValues.length > 0) {
                inputValues.forEach(function (result) {
                    var valueDiv = $('<div>' + result + '</div>');
                    CaValuesResults.append(valueDiv);
                    valueDiv.on('click', function () {
                        input.val(result);
                        CaValuesResults.empty();
                    });
                });
            }
        }
    }



    $(document).on('focus', '.dynamic_attributes_row input[id^="location-equipment-"]', function () {
        if (selectedInput && selectedInput !== this) {
            var otherAutocompleteResults = $(selectedInput).parent().find('.cavalues_results');
            otherAutocompleteResults.empty();
        }
        selectedInput = $(this);
        fetchCaValues(selectedInput.data('id'), function (id) {
            displayCaValuesResults(selectedInput);
        });
    });

    $(document).on('input', '.dynamic_attributes_row input[id^="location-equipment-"]', function () {
        displayCaValuesResults($(this));
    });

    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>
<style>
    <?php ob_start();

    ?>
    .layervalues_results,
    .cavalues_results {
        position: absolute;
        background-color: white;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        width: 100%;
    }

    .layervalues_results div,
    .cavalues_results div {
        padding: 7px 10px;
    }

    .form-group {
        position: relative
    }

    .layervalues_results div,
    .cavalues_results div {
        padding: 5px;
        cursor: pointer;
    }

    .layervalues_results div:hover,
    .cavalues_results div:hover {
        background-color: #f0f0f0;
    }

    .layervalues_results div.no-results,
    .cavalues_results div.no-results {
        padding: 5px;
        color: #888;
    }

    <?php $css = ob_get_clean();
    ?>
    <?php $this->registerCss($css);
    ?>
</style>