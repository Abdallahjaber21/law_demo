<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use yii\grid\GridView;
use common\components\extensions\RelationColumn;
use common\models\Account;
use common\models\Admin;
use yii\helpers\ArrayHelper;
use common\models\Division;
use common\models\Equipment;
use common\models\Location;
use common\models\MainSector;
use common\models\Sector;
use common\models\SegmentPath;
use common\models\Technician;
use common\models\UserAudit;
use yii\helpers\Url;
use rmrevin\yii\fontawesome\FA;
use common\widgets\inputs\assets\ICheckAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Locations';
$this->params['breadcrumbs'][] = $this->title;

$pageId = Yii::$app->controller->id;
$modelname = Location::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="location-index" style="padding:10px">

    <div class="row">
        <div class="col-md-12">
            <div style="background:#000;color:#fff;text-align:center">
                <?php $panel = PanelBox::begin([
                    'title' => '<span style="color:#fff">' . $this->title . '</span>',
                    'icon' => 'table',
                ]);
                ?>
            </div>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->id;
                        },
                        'format' => 'raw',
                        'visible' => !in_array('id', $hiddenAttributes),
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                    ],


                    [
                        'attribute' => 'name',
                        'enableSorting' => false,
                        'visible' => !in_array('name', $hiddenAttributes),
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                    ],

                    [
                        'attribute' => 'code',
                        'enableSorting' => false,
                        'visible' => !in_array('code', $hiddenAttributes),
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'division_id',
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                        'format' => 'html',
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) && !in_array('division_id', $hiddenAttributes),
                        'value' => function ($model) {
                            if (!empty($model->division->name)) {
                                return $model->division->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'main_sector_id',
                        'enableSorting' => false,
                        'label' => 'Main Sector',
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                        'format' => 'html',
                        'visible' => ((empty(@Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminDivisionID() == Division::DIVISION_VILLA))) && !in_array('main_sector_id', $hiddenAttributes),
                        'value' => function ($model) {
                            return @$model->sector->mainSector->name;
                        },
                        'data' => (@Account::getAdminDivisionID() == Division::DIVISION_VILLA) ? ArrayHelper::map(MainSector::find()->where(['division_id' => Account::getAdminDivisionID()])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name') : ArrayHelper::map(MainSector::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'sector_id',
                        'enableSorting' => false,
                        'visible' => !in_array('sector_id', $hiddenAttributes),
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->sector->name)) {
                                return $model->sector->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Technician::getTechnicianSectorsOptions(), 'id', 'name')
                    ],
                    [
                        'attribute' => 'equipment_path_id',
                        'enableSorting' => false,
                        'visible' => !in_array('equipment_path_id', $hiddenAttributes),
                        'value' => function ($model) {
                            if (@$model->segmentPath)
                                return SegmentPath::getLayersValue(@$model->segmentPath->value);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'status',
                        'enableSorting' => false,
                        'visible' => !in_array('status', $hiddenAttributes),
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'enableSorting' => false,
                        'visible' => !in_array('created_at', $hiddenAttributes),
                        'class' => common\components\extensions\DateColumn::class,

                    ],
                    [
                        'attribute' => 'address',
                        'enableSorting' => false,
                        'visible' => !in_array('address', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'latitude',
                        'enableSorting' => false,
                        'visible' => !in_array('latitude', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'longitude',
                        'enableSorting' => false,
                        'visible' => !in_array('longitude', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'owner',
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'owner_phone',
                        'enableSorting' => false,
                        'visible' => !in_array('owner_phone', $hiddenAttributes) && (@Account::getAdminAccountTypeDivisionModel()->name === "Villa" || empty(Account::getAdminAccountTypeDivisionModel())),
                    ],


                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<style>
    <?php ob_start();

    ?>.minus-header {
        bakgorund: #000 color:#fff
    }

    th a {
        color: #fff
    }

    .box-title {
        color: #fff
    }



    <?php $css = ob_get_clean();
    ?><?php $this->registerCss($css);
    ?>
</style>