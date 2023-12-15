<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">
    
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
<?= <<<PANEL
            <?php
            \$panel = PanelBox::begin([
                        'title' => Html::encode(\$this->title),
                        //'icon' => 'plus',
                        'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
PANEL;
?>

            <?php echo '<?php $panel->beginHeaderItem() ?>' ?>
    
            <?php echo '<?= $form->languageSwitcher($model); ?>  ' ?>
    
            <?php echo '<?php $panel->endHeaderItem() ?>' ?>
    

<div class="row">
<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {?>
        <?php if(in_array($attribute, ['created_at', 'updated_at','created_by', 'updated_by'])){continue;} ?>
            <div class="col-sm-6">
            <?php if($attribute == "status"){ ?>
                <?= "<?= " ?>
                    $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $model->status_list,
                    ])
                ?>
            <?php } else { ?>
            <?php echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n";?>
            <?php } ?>
            </div>
    <?php
    }
} 
?>
</div>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
    </div>

<?= <<<PANEL
            <?php PanelBox::end() ?>
PANEL;
?>
    <?= "<?php " ?>ActiveForm::end(); ?>
    
        </div>
    </div>
</div>

