<?php

use common\config\includes\P;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MaintenanceTaskGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Maintenance Task Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="maintenance-task-group-view">


    <div class="row">
        <div class="col-md-4">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon'  => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::MISC_MANAGE_ALL_BARCODES)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?= DetailView::widget([
                'model'      => $model,
                'attributes' => [
                    'id',
                    'code',
                    'name',
                    'group_order',
                    [
                        'attribute' => 'status',
                        'value'     => $model->status_label
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                    //                    'created_by',
                    //                    'updated_by',
                ],
            ]) ?>

            <?php PanelBox::end() ?>
        </div>

        <div class="col-md-8">

            <?php
            $panel = PanelBox::begin(['title' => "Tasks",
                                      'icon'  => 'eye',
                                      'color' => PanelBox::COLOR_GRAY]);
            ?>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
