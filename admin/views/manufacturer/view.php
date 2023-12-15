<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\Manufacturer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manufacturers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manufacturer-view">

    
    <div class="row">
                <div class="col-md-6 col-md-offset-3">

    <?php
    $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
    ]);
    ?>
    <?php if (\common\config\includes\P::c(\common\config\includes\P::MISC_MANAGE_LINE_ITEMS)) { ?>
        <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
    <?php } ?>
    <?php if (\common\config\includes\P::c(\common\config\includes\P::MISC_MANAGE_LINE_ITEMS)) { ?>
        <?php
        /*$panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])*/
        ?>
    <?php } ?>    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
                'id',
            'code',
            'name',
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
