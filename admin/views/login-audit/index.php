<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\models\LoginAudit;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoginAuditSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Login Audits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-audit-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],

                    'ip_address',
                    'login_credential',
                    // 'login_status_label',
                    [
                        'attribute' => 'login_status_label',
                        'value' => function ($model) {
                            return $model->login_status_label;
                        },
                        'filter'    => Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'login_status',
                            'data'          => [
                                LoginAudit::LOGIN_SUCCESS => 'Success',
                                LoginAudit::LOGIN_DENIED => 'Failed',
                            ],
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => 'Select Status'
                            ],
                        ])
                    ],
                    [
                        'attribute' => 'datetime',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    [
                        'attribute' => 'logout',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    // [
                    //     'class' => ActionColumn::className(),
                    //     'template' => '{view} {update} {delete}',
                    //     'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    // ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>