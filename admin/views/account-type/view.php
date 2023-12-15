<?php

use common\config\includes\P;
use common\models\Admin;
use common\models\Division;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\AccountType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Account Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-type-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_UPDATE)) { ?>
            <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'label',
                    [
                        'attribute' => 'division_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            return @Division::findOne(@$model->division_id)->name;
                        },
                    ],
                    [
                        'attribute' => 'role_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            return @$model->role_id;
                        },
                    ],
                    [
                        'attribute' => 'parent_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            return @$model->parent->name;
                        },
                    ],
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

            <?php PanelBox::end() ?> </div>
    </div>
</div>