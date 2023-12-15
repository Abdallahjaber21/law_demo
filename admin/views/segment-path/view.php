<?php

use common\config\includes\P;
use common\models\Admin;
use common\models\SegmentPath;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\SegmentPath */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Segment Paths', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="segment-path-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_UPDATE)) { ?>
            <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'code',
                    [
                        'attribute' => 'value',
                        'value' => function ($model) {
                            return SegmentPath::getLayersValue($model->value);
                        }
                    ],
                    [
                        'attribute' =>  'sector_id',
                        'value' => function ($model) {
                            if ($model->sector)
                                return Html::a("{$model->sector->code} - {$model->sector->name}", Url::to(['sector/view', 'id' => $model->sector_id]));
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'status',
                        'value' => $model->status_label
                    ],
                    'description',
                    'created_at:datetime',
                    'updated_at:datetime',
                    [
                        'attribute' => 'created_by',
                        'label'     => 'Created By',
                        'value'     => function ($model) {
                            return @Admin::findOne($model->created_by)->name;
                        },

                    ],
                    [
                        'attribute' => 'updated_by',
                        'label'     => 'Updated By',
                        'value'     => function ($model) {
                            return @Admin::findOne($model->created_by)->name;
                        },

                    ],
                ],
            ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>