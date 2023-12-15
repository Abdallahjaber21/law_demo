<?php

use common\config\includes\P;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use common\models\Shift;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\User;
use common\models\users\Account;
use common\models\users\Admin;
/* @var $this yii\web\View */
/* @var $model common\models\Shift */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Shifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::CONFIGURATIONS_SHIFT_PAGE_UPDATE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>

            <?php
            $add_updated_by = Yii::$app->cache->get("cashing_user_name"); //to retreive the cash values
            if (empty($add_updated_by)) { //check if the cash is empty e have 3 different table related to user
                $adminUserCache = ArrayHelper::map(Admin::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
                $technicianUserCache = ArrayHelper::map(Technician::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
                $accountsCache = $adminUserCache + $technicianUserCache; //merg into one array
                Yii::$app->cache->set("accounts-cache", $accountsCache, 60 * 30);
            }
            ?> <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'name',
                        'from_hour',
                        'to_hour',

                        [
                            'attribute' => 'description',
                            'value' => function ($model) {
                                if (empty($model->description)) {
                                    return '-';
                                } else {
                                    return $model->description;
                                }
                            },
                        ],                   [
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
                            'value'     => function (Shift $model) {

                                if (!empty($accountsCache[$model->created_by])) {
                                    return $accountsCache[$model->created_by];
                                }
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
                            'value'     => function (Shift $model) {

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