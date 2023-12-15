<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Account;
use common\models\Admin;
use common\models\Category;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\UserAudit;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use rmrevin\yii\fontawesome\FA;
use common\widgets\inputs\assets\ICheckAsset;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipments';
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Equipment::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="equipment-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <div class="button-container <?php echo (P::c(P::MANAGEMENT_EQUIPMENT_PAGE_NEW)) ? '' : 'buttonpostion'; ?>">
                <?php if (P::c(P::MANAGEMENT_EQUIPMENT_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat', 'style' => 'margin-right:10px']);
                }

                $panel->addButton(Yii::t('app', FA::i(FA::_EDIT) . ' Edit'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px', 'id' => 'show-buttons']);
                $panel->addButton(Yii::t('app', 'Cancel'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px;display:none', 'id' => 'cancel_btn']);
                ?>
                <?php if ((P::c(P::MANAGEMENT_EQUIPMENT_PAGE_EXPORT))) { ?>
                    <div class="form-group ">
                        <?= Html::beginForm(['equipment/index'] + Yii::$app->request->queryParams, 'GET', ['class' => 'form-inline', 'id' => 'date-range-form']) ?>
                        <?= Html::submitButton(
                            Yii::t('app', 'Export PDF'),
                            [
                                'form' => 'date-range-form',
                                'class' => 'btn btn-danger btn-flat btn-sm',
                                'name' => 'export',
                                'value' => 'pdf',
                                'target' => '_blank',
                            ]
                        ); ?>


                        <?= Html::endForm() ?>
                    </div><?php } ?>
                <?= $this->render('@app/views/common/_hidden', [
                    'hiddenAttributes' => $hiddenAttributes,
                    'model' => $searchModel,
                ]) ?>

            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'visible' => !in_array('id', $hiddenAttributes),
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                        'value' => function ($model) {
                            if (P::c(P::MANAGEMENT_EQUIPMENT_PAGE_VIEW)) {
                                return Html::a($model->id, Url::to(['view', 'id' => $model->id]));
                            } else {
                                return $model->id;
                            }
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'name']
                    ],
                    [
                        'attribute' => 'code',
                        'visible' => !in_array('code', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'code']
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'division_id',
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'division_id'],
                        'format' => 'html',
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) && !in_array('division_id', $hiddenAttributes),
                        'value' => function ($model) {
                            if (!empty($model->division->name)) {
                                return $model->division->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'attribute' => 'category_id',
                        'visible' => !in_array('category_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'category_id'],
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->category)) {
                                return $model->category->name;
                            }
                        },
                        'filter'    => Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'category_id',
                            'data'          => ArrayHelper::map(Category::find()->orderBy(['name' => SORT_ASC])->all(), 'id', function ($model) {
                                return "{$model->name}";
                            }),
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => 'Select Category'
                            ],
                        ])
                    ],
                    [
                        'attribute' => 'equipment_type_id',
                        'visible' => !in_array('equipment_type_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'equipment_type_id'],
                        'value' => function ($model) {
                            if ($model->equipmentType)
                                return !empty($model->equipmentType->code) ? $model->equipmentType->code . ' - ' . $model->equipmentType->name : $model->equipmentType->name;
                        },
                        'filter'    => Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'equipment_type_id',
                            'data'          => ArrayHelper::map(EquipmentType::find()->orderBy(['name' => SORT_ASC])->all(), 'id', function ($model) {
                                return !empty($model->code) ? $model->code . '-'  .  $model->name :  $model->name;
                            }),
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => 'Select Equipment Type'
                            ],
                        ])
                    ],


                    // [
                    //     'attribute' => 'equipment_path_id',
                    //     'value' => function ($model) {
                    //         if (@$model->equipmentPath)
                    //             return Equipment::getLayersValue(@$model->equipmentPath->value);
                    //     },
                    //     'format' => 'raw',
                    // ],
                    [
                        'attribute' => 'status',
                        'visible' => !in_array('status', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'status'],
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'visible' => !in_array('created_at', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'created_at'],
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],


                    // 'equipment_path_id',
                    // 'category_id',
                    // 'description:ntext',
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete} {audit}',
                        'buttons' => [
                            // 'audit' => function ($url, $model, $key) {
                            //     $url = Yii::$app->urlManager->createUrl(['user-audit/index', 'class_id' => UserAudit::CLASS_NAME_EQUIPMENT, 'entity_row_id' => $model->id]);
                            // },
                            'delete' => function ($url, $model, $key) {
                                if (P::c(P::MANAGEMENT_EQUIPMENT_PAGE_DELETE)) {
                                    if ($model->status == Equipment::STATUS_DELETED) {
                                        return Html::a('Undelete', $url, [
                                            'title' => Yii::t('yii', 'Undelete'),
                                            'class' => 'btn btn-xs btn-warning',
                                            'style' => 'min-width:53px;margin-right:0px',
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
                                }
                            },

                        ],
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'permissions' => [
                            'view' => P::MANAGEMENT_EQUIPMENT_PAGE_VIEW,
                            'update' => P::MANAGEMENT_EQUIPMENT_PAGE_UPDATE,
                            'enable' =>  P::MANAGEMENT_EQUIPMENT_PAGE_UPDATE,
                            'disable' =>  P::MANAGEMENT_EQUIPMENT_PAGE_UPDATE,
                            'delete' =>  P::MANAGEMENT_EQUIPMENT_PAGE_DELETE,
                            'audit' =>  P::MANAGEMENT_EQUIPMENT_PAGE_AUDIT,

                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<style>
    .button-container {
        /* right: 197px !important */
    }
</style>
<?php ICheckAsset::register($this) ?>
<script>
    <?php ob_start(); ?>
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'icheckbox_square-blue',
        increaseArea: '20%'
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>