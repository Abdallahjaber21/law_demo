<?php

use common\components\extensions\Select2;
use common\widgets\dashboard\PanelBox;
use dosamigos\ckeditor\CKEditor;
use yeesoft\multilingual\widgets\ActiveForm;
use yii\helpers\Html;

//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form ActiveForm */
?>

<div class="article-form">

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
                    <?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'subtitle')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'external_link')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $model->status_list,
                    ])
                    ?>
                </div>
                <div class="col-sm-12">
                    <?php ?>
                    <?= $form->field($model, 'content')->widget(CKEditor::className(), [
                        'options' => ['rows' => 6],
                        'preset' => 'custom',
                        'clientOptions' => [
                            "toolbarGroups " => [
                                ["name" => 'clipboard', "groups" => ['undo', 'clipboard']],
                                ["name" => 'editing', "groups" => ['find', 'selection', 'spellchecker', 'editing']],
                                ["name" => 'links', "groups" => ['links']],
                                ["name" => 'insert', "groups" => ['insert']],
                                ["name" => 'forms', "groups" => ['forms']],
                                ["name" => 'tools', "groups" => ['tools']],
                                ["name" => 'document', "groups" => ['mode', 'document', 'doctools']],
                                ["name" => 'others', "groups" => ['others']],
                                '/',
                                ["name" => 'basicstyles', "groups" => ['basicstyles', 'cleanup']],
                                ["name" => 'paragraph', "groups" => ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']],
                                ["name" => 'styles', "groups" => ['styles']],
                                ["name" => 'colors', "groups" => ['colors']],
                                ["name" => 'about', "groups" => ['about']]
                            ],
                            "removeButtons" => 'Image,Copy,Paste,PasteFromWord,PasteText,Scayt,Cut'
                        ]
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?>    <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

