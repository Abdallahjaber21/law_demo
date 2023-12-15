<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentCaValue */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equipment Ca Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-ca-value-view">


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
            <?php } ?> <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                [
                                    'attribute' => 'equipment_ca_id',
                                    'value'     => Html::tag("p", $model->equipmentCa->name),
                                    'format'    => 'html'
                                ],
                                [
                                    'attribute' => 'equipment_id',
                                    'value'     => Html::tag("p", $model->equipment->name),
                                    'format'    => 'html'
                                ],            'value',
                                [
                                    'attribute' => 'status',
                                    'value' => $model->status_label
                                ],                    'created_at:datetime',                    'updated_at:datetime',            'created_by',
                                'updated_by',
                            ],
                        ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>