<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\Select2;
use common\config\includes\P;
use common\models\RepairRequest;
use common\models\search\RepairRequestSearch;
use common\models\users\Admin;
use common\widgets\dashboard\PanelBox;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $from string */
/* @var $to string */

/* @var $pendingRepairRequests RepairRequest[] */
/* @var $completedRepairRequests RepairRequest[] */
/* @var $departedRepairRequests RepairRequest[] */

$todaysServices = [];
$upcomingDays = [];
foreach ($completedRepairRequests as $index => $completedRepairRequest) {
    if (date("Y-m-d", strtotime($completedRepairRequest->scheduled_at)) > date("Y-m-d")) {
        $upcomingDays[] = $completedRepairRequest;
    } else {
        $todaysServices[] = $completedRepairRequest;
    }
}

$this->title = 'Dashboard';
?>
<div class="row">
    <div class="col-md-12">
        <?php
        $panel = PanelBox::begin([
            'title' => $this->title,
            'icon'  => 'dashboard',
            //'body' => false,
            'color' => PanelBox::COLOR_BLUE
        ]);
        ?>
        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_NEW)) {
            $panel->addButton(Yii::t('app', 'New'), ['repair-request/create'], ['class' => 'btn btn-primary btn-flat']);
        }
        ?>

        <?= Html::beginForm(['site/index'], 'GET', ['class' => 'form-inline']) ?>

        <div class="form-group">
            <label for="filterID">Filter By</label>
        </div>
        <div class="form-group">
            <label for="filterID">ID</label>
            <input type="text" class="form-control" id="filterID" placeholder="ID" name="id"
                value="<?= Yii::$app->request->get("id") ?>">
        </div>
        <?php $searchModel = new RepairRequestSearch(); ?>
        <div class="form-group">
            <label for="filterSector">Sectors</label>
        </div>
        <div class="form-group" style="width:200px">
            <?= Select2::widget([
                'name'          => 'sector_id',
                'value'         => Yii::$app->request->get("sector_id"),
                'data'          => Admin::sectorsKeyValList(),
                'pluginOptions' => [
                    'multiple'   => true,
                    'allowClear' => true
                ],
                'options'       => [
                    'placeholder' => 'Sectors',
                ],
            ]) ?>
        </div>
        <div class="form-group">
            <label for="filterTechnician">Technicians</label>
        </div>
        <div class="form-group" style="width:200px">
            <?= Select2::widget([
                'name'          => 'technician_id',
                'value'         => Yii::$app->request->get("technician_id"),
                'data'          => Admin::techniciansKeyValList(),
                'pluginOptions' => [
                    'multiple'   => true,
                    'allowClear' => true
                ],
                'options'       => [
                    'placeholder' => 'Technicians'
                ],
            ]) ?>
        </div>

        <?= Html::submitButton("Filter", ['class' => 'btn btn-flat btn-primary']) ?>
        <?= Html::a("Reset", ['site/index'], ['class' => 'btn btn-flat btn-default']) ?>
        <?= Html::endForm() ?>

        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_PENDING_SERVICES_VIEW)) : ?>
        <h3>Pending Services</h3>
        <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider(['allModels' => $pendingRepairRequests]),
                'filterModel'  => null,
                'rowOptions'   => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                    // if ($model->hasWarning()) {
                    //     return ['class' => 'danger'];
                    // }
                    return ['class' => ''];
                },
                'tableOptions' => ['class' => 'table table-bordered'],
                'columns'      => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],

                    'technician_id',
                    'equipment_id',
                    'service_type',
                    'requested_at',
                    'scheduled_at',
                    // 'informed_at',
                    // 'arrived_at',
                    // 'departed_at',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    // 'problem_id',
                    // 'assigned_at',
                    // 'customer_signature',
                    // 'random_token',
                    // 'completed_at',
                    // 'note',
                    // 'technician_signature',
                    // 'reported_by_name',
                    // 'reported_by_phone',
                    // 'notification_id',
                    // 'completed_by',
                    // 'owner_id',
                    // 'team_leader_id',
                    // 'description:ntext',
                    // 'urgent_status',
                    // 'division_id',
                    // 'project_id',
                    // 'location_id',
                    // 'category_id',
                    // 'repair_request_path',

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>
        <?php endif; ?>

        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_ONGOING_SERVICES_VIEW)) : ?>
        <h3>Ongoing Services</h3>
        <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider(['allModels' => $todaysServices]),
                'filterModel'  => null,
                'rowOptions'   => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                    // if ($model->hasWarning()) {
                    //     return ['class' => 'danger'];
                    // }
                },
                'tableOptions' => ['class' => 'table table-bordered'],
                'columns'      => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],

                    'technician_id',
                    'equipment_id',
                    'service_type',
                    'requested_at',
                    'scheduled_at',
                    // 'informed_at',
                    // 'arrived_at',
                    // 'departed_at',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    // 'problem_id',
                    // 'assigned_at',
                    // 'customer_signature',
                    // 'random_token',
                    // 'completed_at',
                    // 'note',
                    // 'technician_signature',
                    // 'reported_by_name',
                    // 'reported_by_phone',
                    // 'notification_id',
                    // 'completed_by',
                    // 'owner_id',
                    // 'team_leader_id',
                    // 'description:ntext',
                    // 'urgent_status',
                    // 'division_id',
                    // 'project_id',
                    // 'location_id',
                    // 'category_id',
                    // 'repair_request_path',

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>
        <?php endif; ?>

        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_DEPARTED_SERVICES_VIEW)) : ?>
        <h3>Departed repair services</h3>
        <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider(['allModels' => $departedRepairRequests]),
                'filterModel'  => null,
                'rowOptions'   => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                    // if ($model->hasWarning()) {
                    //     return ['class' => 'danger'];
                    // }
                },
                'tableOptions' => ['class' => 'table table-bordered'],
                'columns'      => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],

                    'technician_id',
                    'equipment_id',
                    'service_type',
                    'requested_at',
                    'scheduled_at',
                    // 'informed_at',
                    // 'arrived_at',
                    // 'departed_at',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    // 'problem_id',
                    // 'assigned_at',
                    // 'customer_signature',
                    // 'random_token',
                    // 'completed_at',
                    // 'note',
                    // 'technician_signature',
                    // 'reported_by_name',
                    // 'reported_by_phone',
                    // 'notification_id',
                    // 'completed_by',
                    // 'owner_id',
                    // 'team_leader_id',
                    // 'description:ntext',
                    // 'urgent_status',
                    // 'division_id',
                    // 'project_id',
                    // 'location_id',
                    // 'category_id',
                    // 'repair_request_path',
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>
        <?php endif; ?>

        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_UPCOMING_DAYS_SERVICES_VIEW)) : ?>
        <h3>Upcoming Days Services</h3>
        <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider(['allModels' => $upcomingDays]),
                'filterModel'  => null,
                'rowOptions'   => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                    // if ($model->hasWarning()) {
                    //     return ['class' => 'danger'];
                    // }
                },
                'tableOptions' => ['class' => 'table table-bordered'],
                'columns'      => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],

                    'technician_id',
                    'equipment_id',
                    'service_type',
                    'requested_at',
                    'scheduled_at',
                    // 'informed_at',
                    // 'arrived_at',
                    // 'departed_at',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    // 'problem_id',
                    // 'assigned_at',
                    // 'customer_signature',
                    // 'random_token',
                    // 'completed_at',
                    // 'note',
                    // 'technician_signature',
                    // 'reported_by_name',
                    // 'reported_by_phone',
                    // 'notification_id',
                    // 'completed_by',
                    // 'owner_id',
                    // 'team_leader_id',
                    // 'description:ntext',
                    // 'urgent_status',
                    // 'division_id',
                    // 'project_id',
                    // 'location_id',
                    // 'category_id',
                    // 'repair_request_path',

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>
        <?php endif; ?>

        <?php PanelBox::end() ?>
    </div>
</div>

<style type="text/css">
<?php ob_start() ?>.content-header {
    display: none;
}

.content-wrapper {
    position: relative;
}

.content {
    padding: 0;
}

<?php $css=ob_get_clean() ?><?php $this->registerCss($css) ?>
</style>

<!-- <script>
    <//?php ob_start(); ?>
    setTimeout(function() {
        location.reload();
    }, 30000)
    <//?php $js = ob_get_clean(); ?>
    <//?php $this->registerJs($js); ?>
</script> -->