<?php


use admin\models\ExcelUploadForm;
use common\widgets\dashboard\PanelBox;
use yii\base\DynamicModel;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model ExcelUploadForm */

$this->title = "Fix Barcodes Locations";
?>
<div class="btn-danger">
    WARNING ::: DEV ONLY ::: LEAVE NOW
</div>
<div class="fix-barcodes">

    <div class="location-form">

        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(); ?>
                <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode($this->title),
                    'color' => PanelBox::COLOR_RED
                ]);
                ?>
                <?= $form->field($model, 'file')->fileInput(['accept' => 'xls,xlsx']) ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('admin', 'Upload'), ['class' => 'btn btn-success']) ?>
                </div>
                <?php PanelBox::end() ?>
                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
<div class="btn-danger">
    WARNING ::: DEV ONLY ::: LEAVE NOW
</div>
<br />
<div class="btn-danger">
    WARNING ::: DEV ONLY ::: LEAVE NOW
</div>
<br />
<div class="btn-danger">
    WARNING ::: DEV ONLY ::: LEAVE NOW
</div>
<br />
<div class="btn-danger">
    WARNING ::: DEV ONLY ::: LEAVE NOW
</div>