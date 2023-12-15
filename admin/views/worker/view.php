<?php

use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Worker */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Workers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="worker-view">


    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            <div class="clearfix">
                <?= Html::img($model->image_thumb_url, ['width' => 100, 'class' => 'img-circle pull-right']) ?> </div>
            <br />
        </div>
        <div class="col-md-6">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon'  => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin")) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php if (Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin")) { ?>
                <?php
                $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-flat',
                    'data'  => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method'  => 'post',
                    ],
                ])
                ?>
            <?php } ?> <?= DetailView::widget([
                            'model'      => $model,
                            'attributes' => [
                                'id',
                                'name',
                                'title',
                                'phone_number',
                                [
                                    'attribute' => 'status',
                                    'value'     => $model->status_label
                                ], 'created_at:datetime', 'updated_at:datetime', 'created_by',
                                'updated_by',
                            ],
                        ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>