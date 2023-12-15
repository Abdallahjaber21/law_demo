<?php

use common\config\includes\P;
use common\models\Country;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\User;
use common\models\users\Account;
use common\models\users\Admin;
/* @var $this yii\web\View */
/* @var $model common\models\Country */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;



?>
<div class="country-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::CONFIGURATIONS_COUNTRY_PAGE_UPDATE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'country_code',
                    'currency',
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
                        'value'     => function (Country $model) {

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
                        'value'     => function (Country $model) {

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
                        'data' => ArrayHelper::map(Admin::find()->all(), 'id', 'name')


                    ],


                ],
            ]) ?>


            <?php PanelBox::end() ?> </div>
    </div>
</div>