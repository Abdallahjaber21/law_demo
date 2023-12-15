<?php

use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\models\User;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?><?php if (\common\config\includes\P::c(\common\config\includes\P::MANAGEMENT_USER_PAGE)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'columns'      => [
                    [
                        'class'         => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute'     => 'image',
                        'value'         => function ($model) {
                            return Html::img($model->image_thumb_url, ['width' => '50']);
                        },
                        'format'        => 'html',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute'     => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],
                    'name',
                    'email:email',
                    'phone_number',

                    [
                        'label' => 'Locations',
                        'value' => function (User $model) {
                            return implode(", ", ArrayHelper::getColumn($model->userLocations, "location.name", false));
                        }
                    ],
                    //'address',
                    // 'auth_key',
                    // 'access_token',
                    // 'random_token',
                    // 'password_reset_token',
                    // 'mobile_registration_id:ntext',
                    // 'web_registration_id:ntext',
                    // 'enable_notification',
                    // 'locked',
                    // 'login_attempts',
                    // 'last_login',
                    // 'timezone',
                    // 'language',
                    [
                        'attribute' => 'created_at',
                        'format'    => 'datetime',
                        'class'     => common\components\extensions\DateColumn::class
                    ],
                    [
                        'attribute' => 'status',
                        'class'     => common\components\extensions\OptionsColumn::class
                    ],

                    [
                        'class'         => ActionColumn::className(),
                        'template'      => '{view} {update} ',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>