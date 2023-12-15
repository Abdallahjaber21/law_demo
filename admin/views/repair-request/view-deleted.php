<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\OptionsColumn;
use common\components\extensions\RelationColumn;
use common\components\extensions\Select2;
use common\models\CauseCode;
use common\models\CompletedMaintenanceTask;
use common\models\DamageCode;
use common\models\Equipment;
use common\models\LineItem;
use common\models\MaintenanceTaskGroup;
use common\models\Manufacturer;
use common\models\ObjectCode;
use common\models\RepairRequest;
use common\models\Technician;
use common\widgets\dashboard\PanelBox;
use common\widgets\ImagesGallery;
use kartik\datetime\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RepairRequest */
/* @var $serviceLogs array */

$this->title = $model->service_type_label . " #" . $model->id;
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Services'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$responseTime = 0;
if (!empty($model->arrived_at)) {
    $time = ceil((strtotime($model->arrived_at) - strtotime($model->scheduled_at)) / 60);
    if ($time < 0) {
        $time = 0;
    }
    $responseTime = $time . ' Minutes';
}
?>
<div class="repair-request-view">


    <div class="row">
        <div class="col-md-6">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon'  => 'eye',
                'color' => PanelBox::COLOR_RED
            ]);
            ?>

            <?php
            $problem = @$model->problem->name;
            if (empty($model->problem_id)) {
                if (!empty($model->problem_input)) {
                    $problem = "Other ({$model->problem_input})";
                }
            }
            ?>
            <div class="bg-grey">
                <?= DetailView::widget([
                    'model'      => $model,
                    'attributes' => [
                        'id',
                        'notification_id',
                        [
                            'attribute' => 'related_request_id',
                            'value'     => Html::a("{$model->related_request_id}", ['repair-request/view', 'id' => $model->related_request_id]),
                            'format'    => 'html',
                            'visible'   => !empty($model->related_request_id)
                        ],
                        [
                            'attribute' => 'status',
                            'value'     => Html::tag("h2", "DELETED", ['class' => 'text-bold text-red']),
                            'format'    => 'html'
                        ],
                        'equipment.location.name:text:Location',
                        'equipment.name:text:Equipment',
                        'equipment.material:text:Equipment Material',
                        'equipment.expire_at:text:Expire At',
                        [
                            'attribute'      => 'extra_cost',
                            'label'          => "Extra Charges",
                            'value'          => !empty($model->extra_cost) ? "YES" : "",
                            'format'         => 'html',
                            'contentOptions' => ['style' => '    background-color: #f2dede !important;'],
                            'captionOptions' => ['style' => '    background-color: #f2dede !important;'],
                        ],
                        'reported_by_name',
                        'reported_by_phone',
                        //                        'user.phone_number:text:Phone Number',
                        [
                            'attribute' => "problem_id",
                            'value'     => $problem
                        ],
                        'service_type_label',
                        'schedule_label',
                        //                        'requested_at:datetime',
                        //                        'scheduled_at:datetime',
                        //                        'created_at:datetime',
                        //                        'extra_cost:currency',
                        'rating',
                        [
                            'label'   => 'Response Time',
                            'value'   => $responseTime,
                            'visible' => $model->type == RepairRequest::TYPE_REQUEST
                        ],
                        [
                            'label'   => 'Intervention Time',
                            'value'   => (!empty($model->departed_at) ? (ceil($model->calculateInterventionTime() / 60) . ' Minutes') : null),
                            'visible' => $model->type == RepairRequest::TYPE_REQUEST
                        ],

                        [
                            'label'   => 'Duration',
                            'value'   => (!empty($model->departed_at)) ? (ceil((strtotime($model->departed_at) - strtotime($model->arrived_at)) / 60) . ' Minutes') : null,
                            'visible' => $model->type == RepairRequest::TYPE_SCHEDULED
                        ],
                    ]
                ]) ?>
            </div>
            <?php PanelBox::end() ?>

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon'  => 'eye',
                'color' => PanelBox::COLOR_GREEN
            ]);
            ?>
            <div class="bg-white">
                <?= DetailView::widget([
                    'model'      => $model,
                    'attributes' => [
                        'created_at:datetime',
                        'requested_at:datetime',
                        'scheduled_at:datetime',
                        'assigned_at:datetime',
                        'informed_at:datetime',
                        [
                            'attribute' => 'eta',
                            'value'     => $model->eta ? Yii::$app->getFormatter()->asDatetime($model->eta) . ' ' .
                                (empty($model->arrived_at) ? '(' . Yii::$app->getFormatter()->asRelativeTime($model->eta) . ')' : '')
                                : null
                        ],
                        'arrived_at:datetime',
                        'departed_at:datetime',
                        'completed_at:datetime',
                        'note',
                        'note_client',
                        'rejection_reason',
                    ]
                ])
                ?>
            </div>
            <hr />
            <?php if ($model->type == RepairRequest::TYPE_REQUEST) { ?>

                <?= DetailView::widget([
                    'model'      => $model,
                    'attributes' => [
                        //'status_label',
                        //'user.name:text:User',
                        'technician.name:text:Technician',
                        //                        'system_operational:boolean',
                        [
                            'attribute' => 'system_operational',
                            'format'    => 'raw',
                            'value'     => Yii::$app->formatter->asBoolean($model->system_operational)
                        ],
                        //'eta:datetime',
                        'updated_at:datetime',
                        //                    'created_by',
                        //                    'updated_by',
                    ],
                ]) ?>

            <?php } ?>
            <?php if ($model->type == RepairRequest::TYPE_SCHEDULED) { ?>
                <?= DetailView::widget([
                    'model'      => $model,
                    'attributes' => [
                        //'user.name:text:User',
                        'technician.name:text:Technician',
                        //'works_completed:boolean',
                        [
                            'attribute' => 'works_completed',
                            'format'    => 'raw',
                            'value'     => Yii::$app->formatter->asBoolean($model->works_completed)
                        ],
                        //'eta:datetime',
                        'updated_at:datetime',
                        //                    'created_by',
                        //                    'updated_by',
                    ],
                ]) ?>
            <?php } ?>
            <?php PanelBox::end() ?>
        </div>
        <div class="col-md-6">


            <?php if ($model->type != RepairRequest::TYPE_MAINTENANCE) { ?>
                <?php if ($model->status == RepairRequest::STATUS_COMPLETED || $model->status == RepairRequest::STATUS_COMPLETED) { ?>
                    <div>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Client Note</th>
                                    <td>
                                        <?= Yii::$app->formatter->asText($model->note_client) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Atl Note</th>
                                    <td>
                                        <?= Yii::$app->formatter->asText($model->atl_note) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php
            $logs = $serviceLogs;
            if (!empty($logs)) { ?>

                <?php
                $panel = PanelBox::begin([
                    'title' => "Logs",
                    'icon'  => 'table',
                    'color' => PanelBox::COLOR_ORANGE
                ]);
                ?>
                <table class="table table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Log</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $index => $log) { ?>
                            <tr>
                                <td><?= date("Y-m-d H:i:s", strtotime($log['created_at'] . " UTC")) ?></td>
                                <td><?= $log['user_name'] ?></td>
                                <td><?= $log['log_message'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <?php PanelBox::end() ?>
            <?php } ?>

        </div>
    </div>
</div>
<style>
    <?php ob_start(); ?>.bg-grey table tr td,
    .bg-grey table tr th {
        background-color: #f5f5f5 !important;
    }

    .bg-white table tr td,
    .bg-white table tr th {
        background-color: #fff !important;
    }

    <?php $css = ob_get_clean(); ?><?php $this->registerCss($css); ?>
</style>