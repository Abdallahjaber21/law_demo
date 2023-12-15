<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use yii\grid\GridView;
use common\models\Shift;
use common\models\users\Account;
use common\components\extensions\RelationColumn;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\Admin;
use common\components\helpers\DateHelper;
use common\config\includes\P;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ShiftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shifts';
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $accountsCache = Yii::$app->cache->get("accounts-cache");
if (empty($accountsCache)) {
    $adminCache = ArrayHelper::map(Admin::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
    $technicianCache = ArrayHelper::map(Technician::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
    $accountsCache = $adminCache + $technicianCache;
    Yii::$app->cache->set("accounts-cache", $accountsCache, 60 * 15);
} ?>
<div class="shift-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?> <?php if (P::c(P::CONFIGURATIONS_SHIFT_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            $url = \yii\helpers\Url::to(['shift/view', 'id' => $model->id]);
                            return \yii\helpers\Html::a($model->id, $url);
                        },
                    ],

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
                    ],                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],

                    [
                        'attribute'      => 'created_at',
                        'format'         => 'datetime',
                        'class'          => common\components\extensions\DateRangeColumn::className(),
                        'attribute_from' => 'created_at_from',
                        'attribute_to'   => 'created_at_to',
                        'label'          => 'Created At',


                    ],

                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'created_by',
                        'label'     => 'Created By',
                        'value'     => function (Shift $model) {

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
                        'data'      =>
                        ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'attribute'      => 'updated_at',
                        'format'         => 'datetime',
                        'class'          => common\components\extensions\DateRangeColumn::className(),
                        'attribute_from' => 'updated_at_from',
                        'attribute_to'   => 'updated_at_to',
                        'label'          => 'Updated At',

                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'updated_by',
                        'label'     => 'Updated By',
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
                        'data'      =>
                        ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'permissions' => [
                            'view' => P::CONFIGURATIONS_SHIFT_PAGE_VIEW,
                            'update' => P::CONFIGURATIONS_SHIFT_PAGE_UPDATE,
                            'enable' =>  P::CONFIGURATIONS_SHIFT_PAGE_UPDATE,
                            'disable' =>  P::CONFIGURATIONS_SHIFT_PAGE_UPDATE,
                            'delete' => false
                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>