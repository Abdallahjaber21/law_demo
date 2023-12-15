<?php

use common\config\includes\P;
use common\models\UserLocation;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">


    <div class="row">
        <div class="col-md-1">
            <div class="clearfix">
                <?= Html::img($model->image_thumb_url, ['width' => "100%", 'class' => 'img-circle']) ?> </div>
            <br />
        </div>
        <div class="col-md-5">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon'  => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::MANAGEMENT_USER_PAGE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php if (P::c(P::MANAGEMENT_USER_PAGE)) { ?>
                <?php
                /*$panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])*/
                ?>
            <?php } ?>
            <?=
            DetailView::widget([
                'model'      => $model,
                'attributes' => [
                    'id',
                    'email:email',
                    'name',
                    [
                        'attribute' => 'status',
                        'value'     => $model->status_label
                    ],
                    'phone_number',
                    'floor_number',
                    'job_category',
                    'company_name',
                    'job_title',
                    'birthdate',
                    //'address',
                    //                    'language',
                    //                    'enable_notification:boolean',
                    'created_at:datetime',
                    //                    'updated_at:datetime',
                ],
            ])
            ?>
            <br />
            <?php if (P::c(P::DEVELOPER)) { ?>
                <?=
                DetailView::widget([
                    'model'      => $model,
                    'attributes' => [
                        'locked:boolean',
                        'login_attempts',
                        'last_login',
                        'access_token',
                        'random_token',
                        'auth_key',
                        'mobile_registration_id',
                        'web_registration_id',
                        'platform',
                    ],
                ])
                ?>
            <?php } ?>

            <?php PanelBox::end() ?>
        </div>


        <?php $userLocations = $model->userLocations ?>
        <?php if (!empty($userLocations)) { ?>
            <div class="col-md-6">

                <?php
                $panel = PanelBox::begin([
                    'title' => "Locations",
                    'icon'  => 'building',
                    'color' => PanelBox::COLOR_RED
                ]);
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td>Location</td>
                            <td>Role</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <?php foreach ($userLocations as $index => $userLocation) { ?>
                        <tr>
                            <td>
                                <?= Html::a($userLocation->location->name, ['location/view', 'id' => $userLocation->location_id]) ?>
                            </td>
                            <td><?= $userLocation->role_label ?></td>
                            <td>
                                <?= Html::a("Remove", ['user/remove-from-location', 'user_id' => $model->id, 'location_id' => $userLocation->location_id, '_ref' => 'user'], ['class' => 'btn btn-danger btn-xs']) ?>
                                <?php if ($userLocation->role == UserLocation::ROLE_DECISION_MAKER) { ?>
                                    <?= Html::a("Make Resident", ['user/change-role', 'user_id' => $model->id, 'location_id' => $userLocation->location_id, 'role' => UserLocation::ROLE_RESIDENT, '_ref' => 'user'], ['class' => 'btn btn-info btn-xs']) ?>
                                <?php } else { ?>
                                    <?= Html::a("Make Decision Maker", ['user/change-role', 'user_id' => $model->id, 'location_id' => $userLocation->location_id, 'role' => UserLocation::ROLE_DECISION_MAKER, '_ref' => 'user'], ['class' => 'btn btn-warning btn-xs']) ?>
                                <?php } ?>
                            </td>
                        </tr>

                    <?php } ?>
                </table>

                <?php PanelBox::end() ?>
            </div>
        <?php } ?>

    </div>
</div>