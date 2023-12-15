<?php

use common\components\extensions\ActionColumn;
use common\data\Countries;
use common\models\Account;
use common\models\Division;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\MainSector;
use kartik\select2\Select2;
use rmrevin\yii\fontawesome\FA;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'Photo',
            'visible' => !in_array('image', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'image'],
            'value' => function ($model) {
                $imagePath =  $model->image_thumb_path;
                if (file_exists($imagePath)) {
                    $imageUrl = $model->image_thumb_url;
                } else {
                    $imageUrl = Yii::getAlias('@staticWeb') . '/images/user-default.jpg';
                }
                return Html::img($imageUrl, ['alt' => 'admin', 'width' => '70', 'style' => [
                    'border-radius' => '50%',
                    'margin-left' => 'auto',
                ]]);
            },
            'contentOptions' => ['style' => 'text-align:center;'],
            'format' => 'raw',
            'filter' => false
        ],
        [
            'attribute' => 'id',
            'visible' => !in_array('id', $hiddenAttributes),
            'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
            'format' => 'raw',
            'value' => function ($model) {
                $url = \yii\helpers\Url::to(['admin/view', 'id' => $model->id]);
                return \yii\helpers\Html::a($model->id, $url);
            },
        ],
        [
            'attribute' => 'name',
            'visible' => !in_array('name', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'name'],
        ],
        [
            'attribute' => 'email',
            'visible' => !in_array('email', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'email'],
        ],        [
            'attribute' => 'country',
            'visible' => !in_array('country', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'country'],
            'value' => function ($model) {
                return Countries::getCountryName($model->country);
            },
            'filter'    => Select2::widget([
                'model'         => $searchModel,
                'attribute'     => 'country',
                'data'          => Countries::getCountriesList(),
                'pluginOptions' => [
                    'multiple'   => false,
                    'allowClear' => true
                ],
                'options'       => [
                    'placeholder' => 'Select country'
                ],
            ])
        ],
        [
            'attribute' => 'badge_number',
            'visible' => !in_array('badge_number', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'badge_number'],
            'value' => function ($model) {

                if (!empty($model->badge_number))
                    return Html::tag('div', $model->badge_number, ['class' => 'badge badge-warning']);
            },
            'format' => 'html'
        ],
        [
            'attribute' => 'division_id',
            // 'visible' => empty(Account::getAdminAccountTypeDivisionModel()),
            // 'contentOptions' => ['style' => empty(Account::getAdminAccountTypeDivisionModel()) ? 'display:block;' : 'display:none;'],
            'visible' => !in_array('division_id', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'division_id'],
            'value' => function ($model) {

                if (!empty($model->division_id))
                    return ($model->division->name);
            },
            'format' => 'html',
            'filter'    =>  empty(Account::getAdminAccountTypeDivisionModel()) ? Select2::widget([
                'model'         => $searchModel,
                'attribute'     => 'superadmin_division_id',
                'data'          => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                'pluginOptions' => [
                    'multiple'   => false,
                    'allowClear' => true
                ],
                'options'       => [
                    'placeholder' => ''
                ],
            ]) : false
        ],
        [
            'attribute' => 'main_sector_id',
            // 'visible' => empty(Account::getAdminAccountTypeDivisionModel()),
            // 'contentOptions' => ['style' => empty(Account::getAdminAccountTypeDivisionModel()) ? 'display:block;' : 'display:none;'],
            'visible' => !in_array('main_sector_id', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'main_sector_id'],

            'value' => function ($model) {
                if (!empty($model->mainSector->name)) {
                    $link = ['main-sector/view', 'id' => $model->main_sector_id];
                    return Html::a($model->mainSector->name, $link);
                }
                return null;
            },
            'format' => 'html',
            'filter'    =>  empty(Account::getAdminAccountTypeDivisionModel()) ? Select2::widget([
                'model'         => $searchModel,
                'attribute'     => 'main_sector_id',
                'data'          => ArrayHelper::map(MainSector::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                'pluginOptions' => [
                    'multiple'   => false,
                    'allowClear' => true
                ],
                'options'       => [
                    'placeholder' => ''
                ],
            ]) : false
        ],
        [
            'attribute' => 'account_id',
            'visible' => !in_array('account_id', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'account_id'],
            'value' => function ($model) {
                // $type = $model->account->type;
                // $type_label = @$model->account->admin_account_list[$model->account->type];
                // return $type_label;
                return $model->account->type0->label;
                // if ($type == Account::DEVELOPER) {
                //     return Html::tag('div', FA::i(FA::_USER_MD) . ' ' . $type_label, ['class' => 'badge badge-primary']);
                // } else if ($type == Account::SUPER_ADMIN) {
                //     return Html::tag('div', FA::i(FA::_USER_SECRET) . ' ' . $type_label, ['class' => 'badge badge-warning']);
                // } else if ($type == Account::ADMIN) {
                //     return Html::tag('div', FA::i(FA::_USER_CIRCLE) . ' ' . $type_label, ['class' => 'badge badge-info']);
                // } else if ($type == Account::PLANT_MANAGER) {
                //     return Html::tag('div', FA::i(FA::_APPLE) . ' ' . $type_label, ['class' => 'badge badge-success']);
                // } else if ($type == Account::FLEET_MANAGER) {
                //     return Html::tag('div', FA::i(FA::_BUS) . ' ' . $type_label, ['class' => 'badge badge-purple']);
                // } else if ($type == Account::STORE_KEEPER) {
                //     return Html::tag('div', FA::i(FA::_KEY) . ' ' . $type_label, ['class' => 'badge badge-secondary']);
                // }
            },
            'format' => 'raw',
            // 'class' => common\components\extensions\OptionsColumn::class
            'filter'    => Select2::widget([
                'model'         => $searchModel,
                'attribute'     => 'type',
                'data'          => ArrayHelper::map(Account::getAdminHierarchy(true), 'id', 'label'),
                'pluginOptions' => [
                    'multiple'   => false,
                    'allowClear' => true
                ],
                'options'       => [
                    'placeholder' => ''
                ],
            ])

        ],
        [
            'attribute' => 'status',
            'visible' => !in_array('status', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'status'],
            'class' => common\components\extensions\OptionsColumn::class
        ],
        [
            'attribute' => 'phone_number',
            'visible' => !in_array('phone_number', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'phone_number'],
        ],
        // 'address',
        // [
        //     'attribute' => 'image',
        //     'value' => function ($model) {
        //         return Html::img($model->image_thumb_url, ['width' => '50']);
        //     },
        //     'format' => 'html',
        //     'headerOptions' => ['style' => 'width:50px'],
        // ],
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
            'visible' => !in_array('created_at', $hiddenAttributes),
            'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'created_at'],
            'format' => 'datetime',
            'class' => common\components\extensions\DateColumn::class
        ],

        // 'profession_id',
        // 'badge_number',
        // 'description:ntext',

        [
            'class' => ActionColumn::className(),
            'template' => '{view} {update} {delete} {audit}',
            'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
            'permissions' => [
                'view' => P::ADMINS_ADMIN_PAGE_VIEW,
                'update' => P::ADMINS_ADMIN_PAGE_UPDATE,
                'enable' =>  P::ADMINS_ADMIN_PAGE_UPDATE,
                'disable' =>  P::ADMINS_ADMIN_PAGE_UPDATE,
                'delete' =>  P::ADMINS_ADMIN_PAGE_DELETE,
                'audit' =>  P::ADMINS_ADMIN_PAGE_AUDIT,
            ],
            'buttons' => [
                'view' => function ($url, $model) {
                    if (P::c(P::ADMINS_ADMIN_PAGE_VIEW)) {
                        $options = array_merge([
                            'title' => Yii::t("app", 'View'),
                            'aria-label' => Yii::t("app", 'View'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-primary'
                        ]);
                        return Html::a(\Yii::t("app", 'View'), $url, $options);
                    }
                },
                'update' => function ($url, $model) {
                    if (Account::canManageThisUser($model->account) && P::c(P::ADMINS_ADMIN_PAGE_UPDATE)) {

                        $options = array_merge([
                            'title' => Yii::t("app", 'Update'),
                            'aria-label' => Yii::t("app", 'Update'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs bg-purple'
                        ]);
                        return Html::a(\Yii::t("app", 'Edit'), $url, $options);
                    }
                },
                // 'delete' => function ($url, $model) {
                //     if (Account::canManageThisUser($model->account) && ($model->id != Yii::$app->user->id) && P::c(P::ADMINS_ADMIN_PAGE_DELETE)) {
                //         $options = array_merge([
                //             'title' => Yii::t("app", 'Delete'),
                //             'aria-label' => Yii::t("app", 'Delete'),
                //             'data-confirm' => Yii::t("app", 'Are you sure you want to delete this item?'),
                //             'data-method' => 'post',
                //             'data-pjax' => '0',
                //             'class' => 'btn btn-xs btn-danger'
                //         ]);
                //         return Html::a(\Yii::t("app", 'Delete'), $url, $options);
                //     }
                // },
                'delete' => function ($url, $model, $key) {
                    if (Account::canManageThisUser($model->account) && ($model->id != Yii::$app->user->id) && P::c(P::ADMINS_ADMIN_PAGE_DELETE)) {

                        if ($model->status == \common\models\users\AbstractAccount::STATUS_DELETED) {
                            return Html::a('Undelete', $url, [
                                'title' => Yii::t('yii', 'Undelete'),
                                'class' => 'btn btn-xs btn-warning',
                                'style' => 'min-width:53px;',
                                'data-method' => 'post',
                                'data-confirm' => 'Are you sure you want to undelete this item?',
                            ]);
                        } else {
                            $confirmMessage = 'Are you sure you want to delete this item?';
                            return Html::a('Delete', $url, [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn btn-xs btn-danger',
                                'style' => 'min-width:53px;',
                                'data-method' => 'post',
                                'data-confirm' => $confirmMessage,
                            ]);
                        }
                    }
                },

            ],
        ],
    ],
]); ?>