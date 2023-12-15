<?php

use common\components\extensions\ActionColumn;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\RemovalRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Removal Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="removal-request-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
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
                        'attribute'     => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],
                    [
                        'label' => 'Requester',
                        'attribute' => 'requester_id',
                        'value'     => 'requester.name'
                    ],
                    [
                        'label'     => 'Location',
                        'attribute' => 'user_location_id',
                        'value'     => 'userLocation.location.name'
                    ],
                    [
                        'label'     => 'User',
                        'attribute' => 'user_location_id',
                        'value'     => 'userLocation.user.name'
                    ],
                    'reason',
                    [
                        'attribute' => 'created_at',
                        'format'    => 'datetime',
                        'class'     => common\components\extensions\DateColumn::class
                    ],

                    [
                        'class'         => ActionColumn::className(),
                        'template'      => '{approve} {reject}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                                'approve'=>function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t("app", 'Approve'),
                                        'aria-label' => Yii::t("app", 'Approve'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-xs btn-primary btn-flat'
                                    ];
                                    return Html::a(\Yii::t("app", 'Approve'), $url, $options);
                                },
                                'reject'=>function ($url, $model, $key) {
                                    return Html::a("Reject", ['removal-request/reject-form', 'id'=> $model->id],[
                                        "data-remote" => false,
                                        "data-toggle" => 'modal',
                                        "data-target" => '#ajaxModal',
                                        'class'=>'btn btn-danger btn-xs'
                                    ]);
                                },
                        ]
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<script>
    <?php ob_start() ?>
    // Fill modal with content from link href
    $("#ajaxModal").on("show.bs.modal", function (e) {
        $(this).find(".modal-body").html("");
        var link = $(e.relatedTarget);
        $(this).find(".modal-body").load(link.attr("href"));
    });
    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>