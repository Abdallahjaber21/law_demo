<?php

use common\config\includes\P;
use common\models\Admin;
use common\models\Sector;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\VillaPpmTemplates */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Villa Ppm Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="villa-ppm-templates-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::PPM_VILLA_PPM_TEMPLATES_UPDATE)) { ?>
            <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php if (P::c(P::PPM_VILLA_PPM_TEMPLATES_DELETE)) { ?>
            <?php
                $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-flat',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            <?php } ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    [
                        'attribute' => 'sector_id',
                        'value'     => Html::tag("p", @$model->sector->name),
                        'format'    => 'html'
                    ],
                    [
                        'attribute' => 'location_id',
                        'value' => Html::tag('p', @$model->location->name),
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'category_id',
                        'value' => Html::tag('p', @$model->category->name),
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'asset_id',
                        'value' =>  @$model->asset->code . ' | ' . @$model->asset->equipment->name,
                        'format' => 'html'
                    ],
                    'project_id',
                    'frequency',
                    'repeating_condition',
                    'note',
                    'team_members',
                    'tasks',
                    'next_scheduled_date',
                    'starting_date_time',
                    [
                        'attribute' => 'status',
                        'value' => $model->status_label
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',

                    [
                        'attribute' => 'created_by',
                        'value' => function ($model) {
                            return @Admin::findOne($model->created_by)->name;
                        }
                    ],
                    [
                        'attribute' => 'updated_by',
                        'value' => function ($model) {
                            return @Admin::findOne($model->updated_by)->name;
                        }
                    ],
                ],
            ]) ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>