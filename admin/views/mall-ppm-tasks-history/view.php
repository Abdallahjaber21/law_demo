<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\MallPpmTasksHistory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mall Ppm Tasks Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-ppm-tasks-history-view">


    <div class="row">
                <div class="col-md-6 col-md-offset-3">

                <?php
    $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
    ]);
    ?>
    <?php if (Yii::$app->getUser()->can("developer")) { ?>
        <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
    <?php } ?>
    <?php if (Yii::$app->getUser()->can("developer")) { ?>
        <?php
        $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    <?php } ?>            <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                        'id',
            'task_id',
            'meter_ratio',
            'asset_id',
            'ppm_service_id',
            'year',
            'completed_at',
            'completed_by',
                    [
                        'attribute' => 'status',
                        'value' => $model->status_label
                    ],                    'created_at:datetime',                    'updated_at:datetime',            'created_by',
            'updated_by',
            ],
            ]) ?>

                        <?php PanelBox::end() ?>        </div>
    </div>
</div>