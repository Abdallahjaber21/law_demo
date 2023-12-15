<?php

use common\config\includes\P;
use common\models\Admin;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\EngineOilTypes */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Engine Oil Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="engine-oil-types-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::CONFIGURATIONS_ENGINE_OIL_PAGE_UPDATE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php if (P::c(P::CONFIGURATIONS_ENGINE_OIL_PAGE_DELETE)) { ?>
                <?php
                $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-flat',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            <?php } ?> <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                'oil_viscosity',
                                'motor_fuel_type_id',
                                'can_weight',
                                'oil_durability',
                                [
                                    'attribute' => 'status',
                                    'value' => $model->status_label
                                ],
                                'created_at:datetime',
                                'updated_at:datetime',
                                [
                                    'attribute' => 'created_by',
                                    'label'     => 'Created By',
                                    'value'     => function ($model) {
                                        return Admin::findOne($model->created_by)->name;
                                    },

                                ],
                                [
                                    'attribute' => 'updated_by',
                                    'label'     => 'Updated By',
                                    'value'     => function ($model) {
                                        return Admin::findOne($model->updated_by)->name;
                                    },

                                ],
                            ],
                        ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>