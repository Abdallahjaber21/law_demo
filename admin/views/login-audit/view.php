<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\LoginAudit */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Login Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-audit-view">


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
                                'ip_address',
                                'login_credential',
                                'login_status',
                                'datetime',
                                'logout',
                            ],
                        ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>