<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\RepairRequestChats */
/* @var $form ActiveForm */
?>

<div class="repair-request-chats-form">
    
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
                            <?= $form->field($model, 'request_id')->textInput() ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'assignee_id')->textInput() ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gallery_id')->textInput() ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>
                        </div>
                        <div class="col-sm-6">
                            <?=                     $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $model->status_list,
                    ])
                ?>
                        </div>
                                    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
    </div>

            <?php PanelBox::end() ?>    <?php ActiveForm::end(); ?>
    
        </div>
    </div>
</div>

