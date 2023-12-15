<?php

use common\data\Countries;
use common\models\Account;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use common\models\Admin;
/* @var $this yii\web\View */
/* @var $model common\models\Admin */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Admin::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="admin-view">


    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            <?php if (!in_array('image', $hiddenAttributes)) { ?>
            <div class="clearfix">
                <?= Html::img($model->image_thumb_url, ['width' => 100, 'class' => 'img-circle pull-right']) ?> </div>
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
            <?php if ((Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin") && Account::canManageThisUser($model->account))) { ?>
            <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php /*if ((Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin") && Account::canManageThisUser($model->account)) && ($model->id != Yii::$app->user->id)) { ?>
            <?php
                $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-flat',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            <?php } */ ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'visible'   => !in_array('id', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'name',
                        'visible'   => !in_array('name', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'email',
                        'visible'   => !in_array('email', $hiddenAttributes),
                    ],                    [
                        'attribute' => 'country',
                        'visible'   => !in_array('country', $hiddenAttributes),

                        'value' => function ($model) {
                            return Countries::getCountryName($model->country);
                        }
                    ],
                    // 'password',
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
                        'visible'   => !in_array('address', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'account_id',
                        'visible'   => !in_array('account_id', $hiddenAttributes),
                        'value'     => function ($model) {
                            return $model->account->type0->label;
                        },
                        'format'    => 'html'
                    ],                                // 'auth_key',
                    // 'access_token',
                    // 'random_token',
                    // 'password_reset_token',
                    // 'mobile_registration_id:ntext',
                    // 'web_registration_id:ntext',
                    // 'enable_notification',
                    // 'locked',
                    // 'login_attempts',
                    // 'last_login',
                    'timezone',
                    // 'language',
                    [
                        'attribute' => 'created_at',
                        'visible'   => !in_array('created_at', $hiddenAttributes),
                        'format' => 'datetime',

                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => 'datetime',

                    ],                    [
                        'attribute' => 'division_id',
                        'visible'   => !in_array('division_id', $hiddenAttributes),
                        'value'     => Html::tag("p", @$model->division->name),
                        'format'    => 'html'
                    ],
                    [
                        'attribute' => 'badge_number',
                        'visible'   => !in_array('badge_number', $hiddenAttributes),

                    ],
                    'description:ntext',

                ],
            ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>