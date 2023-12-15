<?php

use common\widgets\dashboard\PanelBox;
use kartik\datetime\DateTimePicker;
use yeesoft\multilingual\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Import maintenance schedule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repair Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="repair-request-update">

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                //'icon' => 'plus',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <p>
                Uploaded csv file should be windows style (separated by commas and encapsuled , by double quotes ")
                <br/>
                csv format is
                <br/>
                |Equipment Code|Technician Email|date(yyyy-mm-dd)|
            </p>
            <?= Html::beginForm('', 'POST', ['enctype' => 'multipart/form-data']) ?>
            <div class="row">
                <div class="col-sm-8">
                    <?= Html::fileInput("import", null, ['class' => 'form-control', 'accept' => '.csv']) ?>
                </div>
                <div class="col-sm-4">
                    <?= Html::submitButton(Yii::t('app', 'Import'), ['class' => 'btn btn-block btn-primary']) ?>
                </div>
            </div>
            <?= Html::endForm() ?>
            <?php PanelBox::end() ?>

        </div>
    </div>
</div>

