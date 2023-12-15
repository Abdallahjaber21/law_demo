<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use yii\grid\GridView;
use common\components\extensions\RelationColumn;
use common\models\Account;
use common\models\Admin;
use yii\helpers\ArrayHelper;
use common\models\Division;
use common\models\Equipment;
use common\models\Location;
use common\models\MainSector;
use common\models\Sector;
use common\models\SegmentPath;
use common\models\Technician;
use yii\helpers\Url;
use rmrevin\yii\fontawesome\FA;
use common\widgets\inputs\assets\ICheckAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Locations';
$this->params['breadcrumbs'][] = $this->title;

$pageId = Yii::$app->controller->id;
$modelname = Location::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="location-index">

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
            <div class="button-container">
                <?php if (P::c(P::MANAGEMENT_LOCATION_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat', 'style' => 'margin-right:10px']);
                }
                $panel->addButton(Yii::t('app', FA::i(FA::_EDIT) . ' Edit'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px', 'id' => 'show-buttons']);
                $panel->addButton(Yii::t('app', 'Cancel'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px;display:none', 'id' => 'cancel_btn']);
                ?>
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
                        'value' => function ($model) {
                            return Html::a($model->id, Url::to(['view', 'id' => $model->id]));
                        },
                        'format' => 'raw',
                        'visible' => !in_array('id', $hiddenAttributes),
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                    ],
                    [
                        'attribute' => 'clone',
                        'label' => 'Clone',
                        'value' => function ($model) {
                            echo $this->render('../location/_clone_modal', [
                                'model' => $model,
                            ]);

                            return Html::button(FA::i(FA::_CLONE) . ' Clone', [
                                'class' => 'btn btn-sm btn-success', 'data-toggle' => 'modal',
                                'data-target' => '#modal-' . $model->id,
                                'id' => '#button-' . $model->id,
                                'disabled' => true,
                                'data-qty' => $model->id
                            ]);
                        },
                        'format' => 'raw'
                    ],

                    [
                        'attribute' => 'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'name'],
                    ],

                    [
                        'attribute' => 'code',
                        'visible' => !in_array('code', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'code'],
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
                        'class' => RelationColumn::className(),
                        'attribute' => 'main_sector_id',
                        'label' => 'main_sector_id',
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'main_sector_id'],
                        'format' => 'html',
                        'visible' => ((empty(@Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminDivisionID() == Division::DIVISION_VILLA))) && !in_array('main_sector_id', $hiddenAttributes),
                        'value' => function ($model) {
                            return @$model->sector->mainSector->name;
                        },
                        'data' => (@Account::getAdminDivisionID() == Division::DIVISION_VILLA) ? ArrayHelper::map(MainSector::find()->where(['division_id' => Account::getAdminDivisionID()])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name') : ArrayHelper::map(MainSector::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'sector_id',
                        'visible' => !in_array('sector_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'sector_id'],
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->sector->name)) {
                                return $model->sector->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Technician::getTechnicianSectorsOptions(), 'id', 'name')
                    ],
                    [
                        'attribute' => 'equipment_path_id',
                        'visible' => !in_array('equipment_path_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'equipment_path_id'],
                        'value' => function ($model) {
                            if (@$model->segmentPath)
                                return SegmentPath::getLayersValue(@$model->segmentPath->value);
                        },
                        'format' => 'raw',
                    ],
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
                        'class' => common\components\extensions\DateColumn::class,

                    ],
                    [
                        'attribute' => 'address',
                        'visible' => !in_array('address', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'address']
                    ],
                    [
                        'attribute' => 'latitude',
                        'visible' => !in_array('latitude', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'latitude']
                    ],
                    [
                        'attribute' => 'longitude',
                        'visible' => !in_array('longitude', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'longitude']
                    ],
                    [
                        'attribute' => 'owner',
                        'visible' => !in_array('owner', $hiddenAttributes) && (@Account::getAdminAccountTypeDivisionModel()->name === "Villa" || empty(Account::getAdminAccountTypeDivisionModel())),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'owner']
                    ],
                    [
                        'attribute' => 'owner_phone',
                        'visible' => !in_array('owner_phone', $hiddenAttributes) && (@Account::getAdminAccountTypeDivisionModel()->name === "Villa" || empty(Account::getAdminAccountTypeDivisionModel())),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'owner_phone']
                    ],

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                $parentClass = 'common\models\Location';
                                $parentId = $model->id;
                                $childClass = 'common\models\LocationEquipments';
                                $paramsAttribute = 'location_id';
                                $canMove = Yii::$app->user->can('management_deleted-entities_permission_move');
                                if ($model->status == Location::STATUS_DELETED) {
                                    if (P::c(P::MANAGEMENT_LOCATION_PAGE_DELETE)) {
                                        return Html::beginForm(['/dynamic/undelete', 'parentClass' => $parentClass, 'parentID' => $parentId, 'childClass' => $childClass, 'paramsAttribute' => $paramsAttribute], 'post') .
                                            Html::submitButton('Undelete', ['class' => 'btn btn-xs btn-warning', 'data-confirm' => 'Are you sure you want to undelete this item?']) .
                                            Html::endForm();
                                    }
                                } else {
                                    if (P::c(P::MANAGEMENT_LOCATION_PAGE_DELETE)) {
                                        return Html::a('Delete', 'javascript:void(0);', [
                                            'class' => 'btn btn-xs btn-danger delete-button',
                                            'data-parent-class' => $parentClass,
                                            'data-parent-id' => $parentId,
                                            'data-child-class' => $childClass,
                                            'data-child-name' => 'Location-Equipments',
                                            'data-url-path' => Yii::$app->request->url,
                                            'data-parent-name' => 'Location',
                                            'style' => 'min-width:57px',
                                            'data-params-attribute' => $paramsAttribute,
                                            'data-deleted-name' => $model->name,

                                        ]);
                                    }
                                }
                            },
                        ],
                        'permissions' => [
                            'view' => P::MANAGEMENT_LOCATION_PAGE_VIEW,
                            'update' => P::MANAGEMENT_LOCATION_PAGE_UPDATE,
                            'enable' =>  P::MANAGEMENT_LOCATION_PAGE_UPDATE,
                            'disable' =>  P::MANAGEMENT_LOCATION_PAGE_UPDATE,
                        ],
                    ],
                    [
                        'attribute' => 'location_equipments',
                        'visible' => P::c(P::MANAGEMENT_LOCATION_PAGE_LOCATIONEQUIPMENT),
                        'label' => 'Location Equipments',
                        'value' => function ($model) {
                            return Html::a('Equipments', Url::to(['location-equipments/index', 'location_id' => $model->id]), ['class' => 'btn btn-xs btn-success']);
                        },
                        'format' => 'raw'
                    ],


                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<script>
    var updateParentStatusUrl = "<?= Url::to(['/dynamic/update-parent-status']) ?>";
</script>
<style>
    td form {
        float: right !important;
        justify-content: space-between !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js">
</script>
<?php $this->registerJsFile("@staticWeb/js/dynamic.js"); ?>
<?php ICheckAsset::register($this) ?>
<script>
    <?php ob_start(); ?>
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-green',
        increaseArea: '20%'
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>