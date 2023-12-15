<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\OptionsColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Category;
use common\models\Professions;
use common\models\Technician;
use common\models\Account;
use common\models\users\Admin;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\widgets\inputs\assets\ICheckAsset;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Category::className();
$attributes = Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="category-index" style="padding:10px">

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
                        'visible' => !in_array('id', $hiddenAttributes),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->id;
                        },
                    ],

                    [
                        'attribute' => 'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'code',
                        'visible' => !in_array('code', $hiddenAttributes),
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'status',
                        'visible' => !in_array('status', $hiddenAttributes),
                        'enableSorting' => false,
                        'class' => OptionsColumn::class
                    ],
                    [
                        'attribute' => 'description',
                        'enableSorting' => false,
                        'visible' => !in_array('description', $hiddenAttributes),
                        'value' => function ($model) {
                            return @$model->description;
                        },
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'parent_id',
                        'visible' => !in_array('parent_id', $hiddenAttributes),
                        'enableSorting' => false,
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->parent)) {
                                $link = ['view', 'id' => $model->parent->id];
                                return Html::a($model->parent->name, $link);
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Category::find()->where(['parent_id' => null])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

                    ],


                    [
                        'attribute'      => 'created_at',
                        'format'         => 'datetime',
                        'visible' => !in_array('created_at', $hiddenAttributes),
                        'enableSorting' => false,
                        'label'          => 'Created At',

                    ],

                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'created_by',
                        'label'     => 'Created By',
                        'enableSorting' => false,
                        'visible' => !in_array('created_by', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'created_by'],
                        'value'     => function (Category $model) {
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
                        'enableSorting' => false,
                        'visible' => !in_array('updated_at', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'updated_at'],


                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'updated_by',
                        'visible' => !in_array('updated_by', $hiddenAttributes),
                        'enableSorting' => false,
                        'label'     => 'Updated By',
                        'value'     => function (Category $model) {
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


                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>