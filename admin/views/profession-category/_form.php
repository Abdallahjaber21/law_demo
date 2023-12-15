<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Category;
use common\models\Profession;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\ProfessionCategory */
/* @var $form ActiveForm */
?>

<div class="profession-category-form">

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
                    <?= $form->field($model, 'profession_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Profession::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => 'Select a profession',
                            'multiple' => true
                        ]
                    ]) ?> </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'category_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Category::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => 'Select a category',
                            'multiple' => true
                        ]
                    ]) ?> </div>

            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?> <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>