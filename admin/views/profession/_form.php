<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use yii\helpers\ArrayHelper;
use common\models\Category;
use common\models\Profession;

/* @var $this yii\web\View */
/* @var $model common\models\Profession */
/* @var $form ActiveForm */
?>
<?php $this->registerCssFile(Yii::getAlias("@staticWeb/scss/style.scss")); ?>


<div class="profession-form">

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin(); ?>
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                //'icon' => 'plus',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php $panel->beginHeaderItem() ?>
            <?= $form->languageSwitcher($model); ?>
            <?php $panel->endHeaderItem() ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?php $statusList = $model->status_list;
                    if ($model->status != Profession::STATUS_DELETED) {
                        unset($statusList[Profession::STATUS_DELETED]);
                    }
                    $disabled = $model->status === Profession::STATUS_DELETED ? ['disabled' => true] : []; ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $statusList,
                        'options' => $disabled,
                    ])
                    ?>
                </div>
                <div class="col-sm-6">
                    <label class="control-label">Categories</label>
                    <?= Select2::widget([
                        'name'    => 'Profession[professionCategory]',
                        'data' => Category::getCategories(),
                        'options' => ['multiple' => true, 'placeholder' => 'Choose Categories', 'id' => 'professionCategory'],
                        'value'   => ArrayHelper::getColumn($model->getProfessionCategories()->select(['category_id'])->asArray()->all(), 'category_id', false)
                    ]) ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                </div>


            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?> <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<script type="text/javascript">
<?php ob_start() ?>
var category_id = "<?= Yii::$app->request->get('category_id'); ?>";
if (category_id != '') {
    $('#professionCategory').val(category_id).trigger('change');
}
<?php $js = ob_get_clean() ?> <?php $this->registerJs($js) ?>
</script>