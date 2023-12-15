<?php

use common\assets\TimePickerAsset;
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use rmrevin\yii\fontawesome\FA;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Shift */
/* @var $form ActiveForm */
?>

<div class="shift-form">

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin(['id' => 'submitform']); ?>
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
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $model->status_list,
                    ])
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'from_hour')->textInput(['class' => 'from_hour form-control', 'onkeydown' => 'return false', 'readonly' => true])->label(Yii::t('app', FA::i(FA::_CLOCK_O) . ' From Hour')) ?>
                </div>
                <div class="col-sm-6">
                    <?php /* $form->field($model, 'to_hour')->widget(TimePicker::className(), ['pluginOptions' => [
                        'autoclose' => true,
                        'autocomplete' => false,
                        'format' => 'yyyy-mm-dd hh:ii:00',
                        'secondStep' => 5,
                    ]])
                   */ ?>
                    <?= $form->field($model, 'to_hour')->textInput(['class' => 'to_hour form-control', 'onkeydown' => 'return false', 'readonly' => true])->label(Yii::t('app', FA::i(FA::_CLOCK_O) . ' To Hour')) ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                </div>

            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? ' submit btn btn-primary btn-flat' : 'submit btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?> <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<style>
    .ui-timepicker-standard .ui-state-hover {
        background-color: #003399;
        background-image: linear-gradient(to bottom, #0088cc, #0044cc);
        background-repeat: repeat-x;
        border: 1px solid #999;
        font-weight: normal;
        color: #fff;
        border-radius: 4px;

    }

    .ui-timepicker-standard .ui-menu-item {
        width: 33% !important;
        float: left;
        list-style: none;
    }

    .ui-timepicker-standard {
        border-radius: 0 0px 10px 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175) !important;
    }

    .ui-timepicker-viewport::-webkit-scrollbar {
        width: 10px;
    }

    .ui-timepicker-viewport::-webkit-scrollbar-thumb {
        background: #EFEFEF;
        border-radius: 20px;
    }

    .ui-timepicker-viewport::-webkit-scrollbar-track {
        background: #fff;
        border-radius: 20px;
    }
</style>
<script>
    <?php ob_start() ?>
    $('.from_hour').timepicker({
        minTime: '00:00',
        maxTime: '23:30',
        'showDuration': false,
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        'timeFormat': 'HH:mm'
    });
    $('.to_hour').timepicker({
        'minTime': '00:00',
        'maxTime': '23:30',
        'showDuration': false,
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        'timeFormat': 'HH:mm'
    });

    $('.submit').on('click', function(e) {
        var toTime = $('.to_hour').val();
        var fromTime = $('.from_hour').val();
        var fromTimeDate = new Date('1970-01-01T' + fromTime);
        var toTimeDate = new Date('1970-01-01T' + toTime);
        if (fromTimeDate > toTimeDate) {
            alert("The from hour should be less than the to hour.");
            e.preventDefault();
        } else {
            $('#submitform').submit();
        }
    });

    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>