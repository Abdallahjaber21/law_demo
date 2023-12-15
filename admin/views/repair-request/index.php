<?php

use common\models\EquipmentType;
use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\OptionsColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Admin;
use common\models\Division;
use common\models\Location;
use common\models\RepairRequest;
use common\models\Technician;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\RepairRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Work Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="repair-request-index">

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
            <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_NEW)) {
                $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
            }
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function ($model, $key, $index, $column) {
                    if ($model->urgent_status == true) {
                        return ['class' => 'danger'];
                    }
                },
                'columns' => [
                    [
                        'attribute' =>  'id',
                        'value' => function ($model) {
                            return Html::a($model->id, Url::to(['view', 'id' => $model->id]));
                        },
                        'format' => 'raw',
                    ],
                    // 'technician_id',
                    [
                        // 'class' => OptionsColumn::class,
                        'attribute' => 'status',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->getStatusTag();
                        },
                        'format' => 'html',
                        'contentOptions' => ['class' => 'td_tag']
                    ],
                    [
                        'attribute' => 'equipment_id',
                        'value' => function ($model) {
                            if (!empty($model->equipment_id)) {
                                $equipment = $model->equipment;
                                return $equipment->code . ' | ' . $equipment->equipment->name . ' | ' . $equipment->equipment->category->name;
                            }
                        },

                    ],

                    [
                        'class' => RelationColumn::className(),

                        'attribute' => 'division_id',
                        'value' => function ($model) {

                            if (!empty($model->division_id)) {
                                return $model->division->name;
                            }
                        },
                        'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                    ],
                    [
                        'class' => RelationColumn::className(),

                        'attribute' => 'location_id',
                        'value' => function ($model) {

                            if (!empty($model->location_id))
                                return $model->location->name;
                        },
                        'data' => ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                    ],
                    // 'urgent_status',

                    [
                        'attribute' => 'repair_request_path',
                        'value' => function ($model) {
                            return $model->repair_request_path;
                        },

                    ],
                    // 'project_id',
                    [
                        'class' => RelationColumn::className(),

                        'attribute' => 'owner_id',
                        'value' => function ($model) {

                            if (!empty($model->owner_id))
                                return $model->owner->name;
                        },
                        'data' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                    ],
                    [

                        'attribute' => 'assignees',
                        'value' => function ($model) {
                            return $model->getAssigneesDetails();
                        },
                        'format' => 'raw',


                    ],
                    [
                        'class' => RelationColumn::className(),

                        'attribute' => 'team_leader_id',
                        'value' => function ($model) {

                            if (!empty($model->team_leader_id))
                                return $model->teamLeader->name;
                        },
                        'data' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')

                    ],
                    'service_note',
                    [
                        'attribute' =>  'created_at',
                        'class' => common\components\extensions\DateColumn::class,
                    ],

                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'created_by',
                        'label'     => 'Created By',
                        'value'     => function ($model) {
                            return $model->getBlamable($model->created_by);
                        },
                        'data' =>  array_merge(
                            ['Admins' => ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                            ['Technicians' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')]
                        ),
                    ],

                    [
                        'attribute' =>  'completed_at',
                        'class' => common\components\extensions\DateColumn::class,
                    ],

                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'completed_by',
                        'label'     => 'Completed By',
                        'value'     => function ($model) {
                            return $model->getBlamable($model->completed_by);
                        },
                        'data' => array_merge(
                            ['Admins' =>  ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                            ['Admins' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>