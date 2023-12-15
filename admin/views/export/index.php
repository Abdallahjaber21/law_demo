<?php

use common\models\users\search\SellerSearch;
use common\widgets\dashboard\DateRangeFilter;
use common\widgets\dashboard\PanelBox;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $searchModel SellerSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Export') . ' ' . $label;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seller-index">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin(); ?>
            <?php
            $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_BLUE
            ]);
            ?>
            <?php $panel->beginHeaderItem() ?>
            <?= DateRangeFilter::widget(['auto_submit' => false, 'defaultRange' => '29']) ?>
            <?php $panel->endHeaderItem() ?>

            <p>
                <?= Yii::t("app", "Select above the date range of the data you want to export, and click Export") ?>
            </p>

            <div class="form-group text-center">
                <?= Html::submitButton(Yii::t("app", "Export"), ['class' => 'btn btn-primary btn-flat']) ?>
            </div>
            <?php PanelBox::end() ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>