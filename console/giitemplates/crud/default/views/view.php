<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$hasImage = false;
foreach ($generator->getTableSchema()->columns as $column) {
    if ($column->name == "image") {
        $hasImage = true;
    }
}
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">


    <div class="row">
        <?php if ($hasImage) { ?>
            <div class="col-md-2 col-md-offset-1">
                <div class="clearfix">
                    <?php echo "<?= Html::img(\$model->image_thumb_url, ['width' => 100, 'class' => 'img-circle pull-right']) ?>"; ?>
                </div>
                <br />
            </div>
        <?php } ?>
        <div class="<?= $hasImage ? "col-md-6" : 'col-md-6 col-md-offset-3' ?>">

            <?php
            $x = <<<PANEL
    <?php
    \$panel = PanelBox::begin([
                'title' => Html::encode(\$this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
    ]);
    ?>
    <?php if (Yii::\$app->getUser()->can("developer")) { ?>
        <?php \$panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => \$model->id], ['class' => 'btn-primary btn-flat']) ?>
    <?php } ?>
    <?php if (Yii::\$app->getUser()->can("developer")) { ?>
        <?php
        \$panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => \$model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    <?php } ?>
PANEL;
            echo $x;
            ?>
            <?= "<?= " ?>DetailView::widget([
            'model' => $model,
            'attributes' => [
            <?php
            if (($tableSchema = $generator->getTableSchema()) === false) {
                foreach ($generator->getColumnNames() as $name) {
                    switch ($column->name) {
                        case 'status':
                            echo <<<ST
                    [
                        'attribute' => 'status',
                        'value' => \$model->status_label
                    ],\n
ST;
                            break;
                        case 'type':
                            echo <<<ST
                    [
                        'attribute' => 'type',
                        'value' => \$model->type_label
                    ],\n
ST;
                            break;
                        case 'created_at':
                            echo <<<ST
                    'created_at:datetime',\n
ST;
                            break;
                        case 'updated_at':
                            echo <<<ST
                    'updated_at:datetime',\n
ST;
                            break;
                        default:
                            echo "            '" . $name . "',\n";
                            break;
                    }
                }
            } else {
                foreach ($generator->getTableSchema()->columns as $column) {
                    if ($column->name == "image") {
                        continue;
                    }
                    $format = $generator->generateColumnFormat($column);
                    switch ($column->name) {
                        case 'status':
                            echo <<<ST
                    [
                        'attribute' => 'status',
                        'value' => \$model->status_label
                    ],
ST;
                            break;
                        case 'type':
                            echo <<<ST
                    [
                        'attribute' => 'type',
                        'value' => \$model->type_label
                    ],
ST;
                            break;
                        case 'created_at':
                            echo <<<ST
                    'created_at:datetime',
ST;
                            break;
                        case 'updated_at':
                            echo <<<ST
                    'updated_at:datetime',
ST;
                            break;
                        default:
                            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                            break;
                    }
                }
            }
            ?>
            ],
            ]) ?>

            <?php
            $x = <<<PANEL
            <?php PanelBox::end() ?>
PANEL;
            echo $x;
            ?>
        </div>
    </div>
</div>