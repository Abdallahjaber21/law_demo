<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>
    
    <div class="row">
        <div class="col-md-12">
            <?= '<?php' ?>
            $panel = PanelBox::begin([
                        'title' => $this->title,
                        'icon' => 'table',
                        'color' => PanelBox::COLOR_GRAY
            ]);
           <?= '?>' ?>
            <?= '<?php' ?>
            if (Yii::$app->getUser()->can("developer")) {
             $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
             } 
        <?= '?>' ?>
            
<?= $generator->enablePjax ? '<?php Pjax::begin(); ?>' : '' ?>
<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'width:50px'],
            ],
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:75px'],
            ],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if($name == "id"){continue;}
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if($column->name == "id"){continue;}
        switch ($column->name) {
            case "image":
?>
[
    'attribute' => 'image',
    'value' => function($model) {
        return Html::img($model->image_thumb_url, ['width' => '50']);
    },
    'format' => 'html',
    'headerOptions' => ['style' => 'width:50px'],
],
<?php
                break;
            case "status":
?>
[
    'attribute' => 'status',
    'class' => common\components\extensions\OptionsColumn::class
],
<?php
                break;
            case "type":
?>
[
    'attribute' => 'type',
    'class' => common\components\extensions\OptionsColumn::class
],
<?php
                break;
            case "created_at":
?>
[
    'attribute' => 'created_at',
    'format' => 'datetime',
    'class'=> common\components\extensions\DateColumn::class
],
<?php
                break;
            case "updated_at":
                break;
            case "updated_by":
                break;
            case "created_by":
                break;
?>
[
    'attribute' => 'updated_at',
    'format' => 'datetime',
    'filter' => kartik\date\DatePicker::widget([
        'name' => Html::getInputName($searchModel, "updated_at"),
        'type' => kartik\date\DatePicker::TYPE_COMPONENT_APPEND,
        'value' => $searchModel->updated_at,
        'layout' => '{input}{remove}',
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ])
],
<?php
                break;

            default:
                if (++$count < 6) {
                    echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                } else {
                    echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                }
                break;
        }
    }
}
?>

            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete}',
                'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
            ],
        ],
    ]); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>
<?= $generator->enablePjax ? '<?php Pjax::end(); ?>' : '' ?>
    
            <?= '<?php' ?> PanelBox::end() <?= '?>' ?>
            
        </div>
    </div>
</div>
