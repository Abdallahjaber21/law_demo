<?php

use common\config\includes\P;
use common\data\Countries;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $model common\models\Technician */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Technicians', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelName = common\models\Technician::class;
$attributes = Account::getHiddenAttributes($pageId, $modelName);
$hiddenAttributes = $attributes['attributeNames'];
?>
<div class="technician-view">


    <div class="row">

        <div class="col-md-2 col-md-offset-1">
            <?php if (!in_array('image', $hiddenAttributes)) { ?>
                <div class="clearfix">
                    <?= Html::img($model->image_thumb_url, ['width' => 100, 'class' => 'img-circle pull-right']) ?>
                </div>
            <?php } ?>

            <br />
        </div>

        <div class="col-md-6">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::MANAGEMENT_TECHNICIAN_PAGE_UPDATE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'visible'   => !in_array('id', $hiddenAttributes),
                    ],                    [
                        'attribute' => 'name',
                        'visible'   => !in_array('name', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'country',
                        'visible'   => !in_array('country', $hiddenAttributes),
                        'value' => function ($model) {
                            return Countries::getCountryName($model->country);
                        }

                    ],
                    [
                        'attribute' => 'status',
                        'visible'   => !in_array('status', $hiddenAttributes),
                        'value' => $model->status_label
                    ],

                    [
                        'attribute' => 'phone_number',
                        'visible'   => !in_array('phone_number', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'address',
                    ],
                    [
                        'attribute' => 'division_id',
                        'value'     => Html::tag("p", $model->division->name),
                        'format'    => 'html',
                        'visible'   => !in_array('division_id', $hiddenAttributes),

                    ],
                    [
                        'attribute' => 'profession_id',
                        'value'     => Html::tag("p", $model->profession->name),
                        'format'    => 'html',
                        'visible'   => !in_array('profession_id', $hiddenAttributes),

                    ],
                    [
                        'attribute' => 'account_type',
                        'visible'   => !in_array('account_type', $hiddenAttributes),
                        'value'     => function ($model) {
                            return @$model->account->type0->label;
                        },
                        'format'    => 'html'
                    ],
                    [
                        'attribute' => 'main_sector_id',
                        'visible'   => !in_array('main_sector_id', $hiddenAttributes),
                        'value' => function ($model) {
                            if ($model->mainSector)
                                return "{$model->mainSector->name}";
                        },
                    ],
                    [
                        'label' => 'Shift',
                        'attribute' => 'shift',
                        'visible'   => !in_array('shift_id', $hiddenAttributes) && ($model->division->has_shifts),
                        'value' => function ($model) {
                            $shift = $model->getTechnicianShifts()->where(['date' => date('Y-m-d')])->one();
                            if ($shift != null) {
                                return $shift->shift->name;
                            }
                        },
                    ],
                    'timezone',
                    [
                        'attribute' => 'created_at',
                        'visible'   => !in_array('created_at', $hiddenAttributes),
                        'format' => 'datetime',
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => 'datetime',
                    ],

                    [
                        'attribute' => 'badge_number',
                        'visible'   => !in_array('created_at', $hiddenAttributes),
                    ],                    'description:ntext',

                ],
            ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>