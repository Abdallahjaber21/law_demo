<?php

use common\config\includes\P;
use common\models\State;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\User;
use common\models\users\Account;
use common\models\users\Admin;

/* @var $this yii\web\View */
/* @var $model common\models\State */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$add_updated_by = Yii::$app->cache->get("cashing_user_name"); //to retreive the cash values
if (empty($add_updated_by)) { //check if the cash is empty e have 3 different table related to user
    $adminUserCache = ArrayHelper::map(Admin::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
    Yii::$app->cache->set("accounts-cache", $adminUserCache, 60 * 30);
}
?>
<div class="state-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::CONFIGURATIONS_STATE_PAGE_UPDATE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'country_id',
                        'value'     => Html::tag("p", @$model->country->name),
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

                    ],                              [
                        'attribute' => 'created_by',
                        'value'     => function (State $model) {
                            if (!empty($model->user_id)) {
                                return "[{$model->user_id}]" . $model->user->name;
                            }
                            if (!empty($adminUserCache[$model->created_by])) {
                                return $adminUserCache[$model->created_by];
                            }
                            if (!empty($model->created_by)) {
                                $account = Account::findOne($model->created_by);
                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['account_id' => $model->created_by])->one();
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
                        'value'     => function (State $model) {
                            if (!empty($model->user_id)) {
                                return "[{$model->user_id}]" . $model->user->name;
                            }
                            if (!empty($accountsCache[$model->updated_by])) {
                                return $accountsCache[$model->updated_by];
                            }
                            if (!empty($model->updated_by)) {
                                $account = Account::findOne($model->updated_by);
                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['account_id' => $model->updated_by])->one();
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