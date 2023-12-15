<?php

use common\config\includes\P;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">


    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            <div class="clearfix">
                <?= Html::img($model->image_thumb_url, ['width' => 100, 'class' => 'img-circle pull-right']) ?>            </div>
            <br/>
        </div>
        <div class="col-md-6">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon'  => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::DEVELOPER)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Send Notification'), ['notify', 'id' => $model->id], ['class' => 'btn-warning btn-flat']) ?>
            <?php } ?>
            <?php if (P::c(P::MISC_MANAGE_NEWS)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?= DetailView::widget([
                'model'      => $model,
                'attributes' => [
                    'id',
                    'title',
                    'subtitle',
                    'content:ntext',
                    [
                        'attribute' => 'status',
                        'value'     => $model->status_label
                    ], 'created_at:datetime', 'updated_at:datetime', 'created_by',
                    'updated_by',
                    'random_token',
                ],
            ]) ?>

            <?php PanelBox::end() ?>        </div>
    </div>
</div>
