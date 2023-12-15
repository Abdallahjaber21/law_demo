<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use yii\helpers\ArrayHelper;
use common\models\Admin;
use yii\grid\GridView;
use common\models\UserAudit;
use rmrevin\yii\fontawesome\FA;


$this->title = 'Audit Trail';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-audit-index">
    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_RED
            ]);
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'user_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            return @$model->user->name;
                        },
                        'data' => ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                    ],

                    [
                        'attribute' => 'class_id',
                        'class' => common\components\extensions\OptionsColumn::class,
                        'filter' => function () {
                            $options = [];

                            if (Yii::$app->user->can('management_location_page_audit')) {
                                $options[UserAudit::CLASS_NAME_LOCATION] = 'Location';
                            }

                            if (Yii::$app->user->can('management_location-equipments_page_audit')) {
                                $options[UserAudit::CLASS_NAME_LOCATIONEQUIPMENT] = 'Location Equipment';
                            }

                            if (Yii::$app->user->can('management_equipment_page_audit')) {
                                $options[UserAudit::CLASS_NAME_EQUIPMENT] = 'Equipment';
                            }
                            if (Yii::$app->user->can('management_technician_page_audit')) {
                                $options[UserAudit::CLASS_NAME_TECHNICIAN] = 'Technician';
                            }
                            if (Yii::$app->user->can('management_equipment-type_page_audit')) {
                                $options[UserAudit::CLASS_NAME_EQUIPMENTTYPE] = 'Equipment Type';
                            }
                            if (Yii::$app->user->can('management_segment-path_page_audit')) {
                                $options[UserAudit::CLASS_NAME_SEGMENTPATH] = 'Segment Path';
                            }
                            if (Yii::$app->user->can('management_professsion_page_audit')) {
                                $options[UserAudit::CLASS_NAME_PROFESSION] = 'Profession';
                            }
                            if (Yii::$app->user->can('configurations_category_page_audit')) {
                                $options[UserAudit::CLASS_NAME_CATEGORY] = 'Category';
                            }
                            if (Yii::$app->user->can('configurations_main-sector_page_audit')) {
                                $options[UserAudit::CLASS_NAME_MAINSECTOR] = 'Main Sector';
                            }
                            if (Yii::$app->user->can('admins_admin_page_audit')) {
                                $options[UserAudit::CLASS_NAME_ADMIN] = 'ADMIN';
                            }

                            if (Yii::$app->user->can('configurations_sector_page_audit')) {
                                $options[UserAudit::CLASS_NAME_SECTOR] = 'Sector';
                            }

                            return $options;
                        },


                    ],

                    [
                        'attribute' => 'entity_row_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            $link = '';
                            if ($model->class_id == UserAudit::CLASS_NAME_LOCATION) {
                                $link = 'location';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_LOCATIONEQUIPMENT) {
                                $link = 'location-equipments';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_EQUIPMENT) {
                                $link = 'equipment';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_EQUIPMENTTYPE) {
                                $link = 'equipment-type';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_TECHNICIAN) {
                                $link = 'technician';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_SEGMENTPATH) {
                                $link = 'segment-path';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_PROFESSION) {
                                $link = 'profession';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_CATEGORY) {
                                $link = 'category';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_MAINSECTOR) {
                                $link = 'main-sector';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_SECTOR) {
                                $link = 'sector';
                            }
                            if ($model->class_id == UserAudit::CLASS_NAME_ADMIN) {
                                $link = 'admin';
                            }
                            $url = ['/' . $link . '/view', 'id' => $model->entity_row_id];
                            return Html::a($model->entity_row_id, $url);

                            return null;
                        },
                    ],
                    //'entity_row_id',
                    [
                        'attribute' => 'action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $action = $model->action;
                            if ($action == "insert") {
                                return Html::tag('div', FA::i(FA::_PLUS_SQUARE_O) . ' ' . $action, ['class' => 'badge badge-success', 'style' => 'min-width:71px']);
                            } else if ($action == 'update') {
                                return Html::tag('div', FA::i(FA::_EDIT) . ' ' . $action, ['class' => 'badge badge-warning', 'style' => 'min-width:71px']);
                            } else if ($action == 'delete') {
                                return Html::tag('div', FA::i(FA::_TRASH) . ' ' . $action, ['class' => 'badge badge-danger', 'style' => 'min-width:71px']);
                            }
                        },
                    ],
                    [
                        'attribute' => 'old_value',

                        'format' => 'raw',
                        'value' => function ($model) {
                            return UserAudit::formatJsonAttributes($model->old_value);
                        },
                    ],
                    [
                        'attribute' => 'new_value',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return UserAudit::formatJsonAttributes($model->new_value);
                        },
                    ],
                    [
                        'attribute'      => 'created_at',
                        'format'         => 'datetime',
                        'class'          => common\components\extensions\DateRangeColumn::className(),
                        'attribute_from' => 'created_at_from',
                        'attribute_to'   => 'created_at_to',
                        'label'          => 'Created At',


                    ],


                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<style>
    /* .grid-view th,
    .table-bordered>tbody>tr>td {
        white-space: pre !important
    } */
</style>