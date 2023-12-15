<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Category;
use common\models\ProfessionCategory;
use common\models\Profession;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form ActiveForm */
?>

<div class="category-form">

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
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?php if (!empty($model->name)) {
                        $parent_id = $model->parent_id;
                        $name = $model->name;
                    } ?>

                    <!-- <?= $form->field($model, 'parent_id')->widget(Select2::className(), [
                                'data' => ['' => ''] + ArrayHelper::map(Category::find()->where(['parent_id' => null])->andWhere(['status' => Category::STATUS_ENABLED])->andWhere(['<>', 'id', $model->id])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                                'options' => ['placeholder' => 'Select Parent Category'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ]) ?> -->
                    <?= $form->field($model, 'parent_id')->widget(Select2::className(), [
                        'data' => ['' => ''] + ArrayHelper::map(Category::find()
                            ->where(['parent_id' => null])
                            ->andWhere(['status' => Category::STATUS_ENABLED])
                            ->andFilterWhere(['<>', 'id', $model->isNewRecord ? null : $model->id])
                            ->orderBy(['name' => SORT_ASC])
                            ->all(), 'id', 'name'),
                        'options' => ['placeholder' => 'Select Parent Category'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]) ?>
                </div>

                <div class="col-sm-6">

                    <?php
                    $statusList = $model->status_list;
                    if ($model->status != Category::STATUS_DELETED) {
                        unset($statusList[Category::STATUS_DELETED]);
                    }
                    $disabled = $model->status === Category::STATUS_DELETED ? ['disabled' => true] : []; ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $statusList, 'options' => $disabled,
                    ])
                    ?>
                </div>
                <div class="col-sm-6">
                    <div class="form-group required">
                        <label class="control-label">Professions</label>
                        <?= Select2::widget([
                            'name'    => 'Category[professionCategory]',
                            'data' => Profession::getProfessions(),
                            'options' => ['multiple' => true, 'placeholder' => 'Choose professions', 'id' => 'professionCategory'],
                            'value'   => ArrayHelper::getColumn($model->getProfessionCategories()->select(['profession_id'])->asArray()->all(), 'profession_id', false)
                        ]) ?>
                        <p class="help-block help-block-error" id="error_profession" style="color:#ec4f56"></p>
                    </div>

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
$('form').submit(function(event) {
    var selectedOptions = $('select[name="Category[professionCategory][]"]').val();
    if (!selectedOptions || selectedOptions.length == 0) {
        event.preventDefault();
        $('#error_profession').text('Professions field is required.');

    } else {
        $('#error_profession').text('');
    }
});
var profession_id = "<?= Yii::$app->request->get('profession_id'); ?>";
if (profession_id != '') {
    $('#professionCategory').val(profession_id).trigger('change');
}
<?php $js = ob_get_clean() ?> <?php $this->registerJs($js) ?>
</script>