<?php

use common\models\City;
use common\models\State;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\User;
use common\models\users\Account;
use common\models\users\Admin;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $model common\models\City */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="city-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::CONFIGURATIONS_CITY_PAGE_UPDATE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'state_id',
                        'value'     => Html::tag("p", @$model->state->name),
                        'format'    => 'html'
                    ],
                    'name',
                    [
                        'attribute' => 'status',
                        'value' => $model->status_label
                    ],


                    [
                        'attribute' => 'created_at',
                        'class' => common\components\extensions\DateColumn::class,
                        'format' => 'datetime',

                    ],
                    [
                        'attribute' => 'updated_at',
                        'class' => common\components\extensions\DateColumn::class,
                        'format' => 'datetime',

                    ],

                    [
                        'attribute' => 'created_by',
                        'value'     => function (City $model) {
                            if (!empty($model->created_by)) {
                                $account = Account::findOne($model->created_by);
                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['id' => $model->created_by])->one();
                                    if (!empty($admin)) {
                                        return ($admin->name);
                                    }
                                }
                            }
                        },
                        'data' =>
                        ArrayHelper::map(Admin::find()->all(), 'id', 'name')

                    ],
                    [
                        'attribute' => 'updated_by',
                        'value'     => function (City $model) {

                            if (!empty($model->updated_by)) {
                                $account = Account::findOne($model->updated_by);
                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['id' => $model->updated_by])->one();
                                    if (!empty($admin)) {
                                        return ($admin->name);
                                    }
                                }
                            }
                        },
                        'data' =>
                        ArrayHelper::map(Admin::find()->all(), 'id', 'name')

                    ],
                ],




            ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>