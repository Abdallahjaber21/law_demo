<?php


/* @var $this View */


use common\components\extensions\Select2;
use common\models\Equipment;
use common\models\Sector;
use common\models\Technician;
use common\widgets\dashboard\PanelBox;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'QR KPIs');
$this->params['breadcrumbs'][] = $this->title;

\common\assets\ChartJsAsset::register($this);

$sector = Yii::$app->request->get("sector");
$technician = Yii::$app->request->get("technician");

?>
<div class="qr-kpis">
    <div class="row row-no-gutters">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => 'QR Scan KPIs',
                'icon'  => 'dashboard',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <?= Html::beginForm(['site/qr-kpis'], 'GET', ['class' => 'form-inline']) ?>
            <?php
            $period = Yii::$app->request->get("period") ? Yii::$app->request->get("period") : 'last-month';

            $from_month = date("Y-m", strtotime(date("Y-m-01") . " -3 days"));
            $to_month = date("Y-m");

            if ($period === 'custom') {
                $from_month = Yii::$app->request->get("from_month") ?
                    Yii::$app->request->get("from_month") : date("Y-m", strtotime(date("Y-m-01") . " -3 days"));
                $to_month = (Yii::$app->request->get("from_month") && Yii::$app->request->get("to_month")) ?
                    Yii::$app->request->get("to_month") : date("Y-m");
            } else if ($period === 'last-quarter') {
                $current_month = (int)date("n");
                //quarters 1-3 / 4-6 / 7-9 / 10-12
                if ($current_month > 9) {//quarter is 7-9
                    $from_month_ = date("Y") . "-07";
                    $to_month = date("Y") . "-09";
                } else if ($current_month > 6) {//quarter is 4-6
                    $from_month = date("Y") . "-04";
                    $to_month = date("Y") . "-06";
                } else if ($current_month > 3) {//quarter is 1-3
                    $from_month = date("Y") . "-01";
                    $to_month = date("Y") . "-03";
                } else {//quarter is 10-12 previous year
                    $prevYear = ((int)date("Y")) - 1;
                    $from_month = $prevYear . "-10";
                    $to_month = $prevYear . "-12";
                }
            } else if ($period === 'last-semester') {
                $current_month = (int)date("n");
                //semesters 1-6 / 6-12
                if ($current_month > 6) {//semester is 1-6
                    $from_month_ = date("Y") . "-01";
                    $to_month = date("Y") . "-06";
                } else {//semester is 7-12 previous year
                    $prevYear = ((int)date("Y")) - 1;
                    $from_month = $prevYear . "-07";
                    $to_month = $prevYear . "-12";
                }
            } else if ($period === 'last-year') {
                $prevYear = ((int)date("Y")) - 1;
                $from_month = $prevYear . "-01";
                $to_month = $prevYear . "-12";
            } else {//default last month
                $from_month = date("Y-m", strtotime(date("Y-m") . "-01 -2 days"));
                $to_month = $from_month;
            }

            $from_month_val = $from_month . '-01';
            $exp = explode("-", $to_month);
            $daysInToMonth = cal_days_in_month(CAL_GREGORIAN, $exp[1], $exp[0]);
            $to_month_val = $to_month . "-" . substr("0" . $daysInToMonth, -2);
            ?>
            <div class="form-group">
                <label class="display-block" for="technician-input">Period</label>
                <?= Html::dropDownList('period', $period,
                    [
                        'last-month'    => 'Last Month',
                        'last-quarter'  => 'Last Quarter',
                        'last-semester' => 'Last Semester',
                        'last-year'     => 'Last Year',
                        'custom'        => 'Custom',
                    ],
                    ['class' => 'form-control', 'id' => 'period-input']) ?>
            </div>
            <div id="custom-date-range" class="form-group hidden">
                <label class="display-block" for="technician-input">Custom Range</label>
                <?= DatePicker::widget([
                    'name'          => "from_month",
                    'name2'         => "to_month",
                    'options'       => ['placeholder' => Yii::t('app', 'From Month')],
                    'options2'      => ['placeholder' => Yii::t('app', 'To Month')],
                    'value'         => $from_month,
                    'value2'        => $to_month,
                    'type'          => DatePicker::TYPE_RANGE,
                    'pluginOptions' => [
                        'autoclose'   => true,
                        'startView'   => 'year',
                        'minViewMode' => 'months',
                        'format'      => 'yyyy-mm',
                        'endDate'     => date('Y-m', strtotime(date('Y-m') . '-01 +3 days')),
                    ]
                ]) ?>
            </div>
            <div class="form-group" style="min-width: 200px">
                <label class="display-block" for="sector-input">Sector</label>
                <?= Select2::widget([
                    'id'            => 'sector-input-2',
                    'name'          => 'sector',
                    'value'         => $sector,
                    'data'          => \common\models\users\Admin::sectorsKeyValList(),
                    'pluginOptions' => [
                        'multiple'   => true,
                        'allowClear' => true
                    ],
                    'options'       => [
                        'placeholder' => 'Select Sectors',
                    ],
                ]) ?>
            </div>
            <div class="form-group hidden">
                <label class="display-block" for="technician-input">Technician</label>
                <?= Html::dropDownList('technician', $technician,
                    \common\models\users\Admin::techniciansKeyValList(),
                    ['class' => 'form-control', 'id' => 'technician-input-2', 'prompt' => 'All']) ?>
            </div>
            <div class="form-group">
                <label class="display-block" for="technician-input">&nbsp;</label>
                <button type="submit" class="btn btn-default">Filter</button>
            </div>
            <?= Html::endForm() ?>
            <br/>
            <?php PanelBox::end() ?>
        </div>
        <div class="col-sm-12 col-md-8 col-lg-9 chart-1">
            <?= $this->render('_barcode-scans-chart', [
                'title'          => 'Elevators',
                'type'           => null,//Equipment::NEW_TYPE_Elevator,
                'codes'          => \common\models\EquipmentMaintenanceBarcode::$elevatorCodes,
                'from_month_val' => $from_month_val,
                'to_month_val'   => $to_month_val,
                'color' => "#1e88e5",
            ]) ?>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-3 chart-2">
            <?= $this->render('_barcode-scans-chart', [
                'title'          => 'BMU',
                'type'           => null,//Equipment::NEW_TYPE_Elevator,
                'codes'          => \common\models\EquipmentMaintenanceBarcode::$bmuCodes,
                'from_month_val' => $from_month_val,
                'to_month_val'   => $to_month_val,
                'color' => "#43a047",
            ]) ?>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-4 chart-3">
            <?= $this->render('_barcode-scans-chart', [
                'title'          => 'Escalators',
                'type'           => null,//Equipment::NEW_TYPE_Elevator,
                'codes'          => \common\models\EquipmentMaintenanceBarcode::$escalatorCodes,
                'from_month_val' => $from_month_val,
                'to_month_val'   => $to_month_val,
                'color' => "#ab000d",
            ]) ?>
        </div>
        <div class="col-sm-12 col-md-2 col-lg-2 chart-4">
            <?= $this->render('_barcode-scans-chart', [
                'title'          => 'RD',
                'type'           => null,//Equipment::NEW_TYPE_Elevator,
                'codes'          => \common\models\EquipmentMaintenanceBarcode::$rdCodes,
                'from_month_val' => $from_month_val,
                'to_month_val'   => $to_month_val,
                'color' => "#5e35b1",
            ]) ?>
        </div>
        <div class="col-sm-12 col-md-1 col-lg-1 chart-5">
            <?= $this->render('_barcode-scans-chart', [
                'title'          => 'GD',
                'type'           => null,//Equipment::NEW_TYPE_Elevator,
                'codes'          => \common\models\EquipmentMaintenanceBarcode::$gdCodes,
                'from_month_val' => $from_month_val,
                'to_month_val'   => $to_month_val,
                'color' => "#f57c00",
            ]) ?>
        </div>
        <div class="col-sm-12 col-md-2 col-lg-2 chart-6">
            <?= $this->render('_barcode-scans-chart', [
                'title'          => 'SR',
                'type'           => null,//Equipment::NEW_TYPE_Elevator,
                'codes'          => \common\models\EquipmentMaintenanceBarcode::$srCodes,
                'from_month_val' => $from_month_val,
                'to_month_val'   => $to_month_val,
                'color' => "#00acc1",
            ]) ?>
        </div>
        <div class="col-sm-12 col-md-1 col-lg-1 chart-7">
            <?= $this->render('_barcode-scans-chart', [
                'title'          => 'Ladder',
                'type'           => null,//Equipment::NEW_TYPE_Elevator,
                'codes'          => \common\models\EquipmentMaintenanceBarcode::$ladderCodes,
                'from_month_val' => $from_month_val,
                'to_month_val'   => $to_month_val,
                'color' => "#795548",
            ]) ?>
        </div>
        <div class="col-sm-12 col-md-2 col-lg-2 chart-8">
            <?= $this->render('_barcode-scans-chart', [
                'title'          => 'SG',
                'type'           => null,//Equipment::NEW_TYPE_Elevator,
                'codes'          => \common\models\EquipmentMaintenanceBarcode::$sgCodes,
                'from_month_val' => $from_month_val,
                'to_month_val'   => $to_month_val,
                'color' => "#9c27b0",
            ]) ?>
        </div>
    </div>
</div>
<script>
    <?php ob_start() ?>
    $("#period-input").change(function () {
        if ($("#period-input").val() == "custom") {
            $("#custom-date-range").removeClass("hidden");
        } else {
            $("#custom-date-range").addClass("hidden");
        }
    });
    $("#period-input").trigger("change");
    $('[data-toggle="tooltip"]').tooltip();
    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>
<style>
    <?php ob_start() ?>
    .box{
        border: 1px solid #ccc;
        margin: 0;
    }
    .content-header {
        display: none;
    }
    .content {
        padding: 0;
    }

    @media (min-width: 768px) {
        .chart-1 {width: 100% !important;}
        .chart-2 {width: 50% !important;}
        .chart-3 {width: 50% !important;}
        .chart-4 {width: 25% !important;}
        .chart-5 {width: 25% !important;}
        .chart-6 {width: 25% !important;}
        .chart-7 {width: 25% !important;}
        .chart-8 {width: 25% !important;}
    }
    @media (min-width: 992px) {
        .chart-1 {width: 100% !important;}
        .chart-2 {width: 40% !important;}
        .chart-3 {width: 40% !important;}
        .chart-4 {width: 20% !important;}
        .chart-5 {width: 20% !important;}
        .chart-6 {width: 20% !important;}
        .chart-7 {width: 20% !important;}
        .chart-8 {width: 20% !important;}
    }

    @media (min-width: 1200px) {
        .chart-1 {width: 78% !important;}
        .chart-2 {width: 22% !important;}
        .chart-3 {width: 38% !important;}
        .chart-4 {width: 12% !important;}
        .chart-5 {width: 12% !important;}
        .chart-6 {width: 14% !important;}
        .chart-7 {width: 12% !important;}
        .chart-8 {width: 12% !important;}
    }
    .tooltip-inner {
        max-width: 550px;
        /* If max-width does not work, try using width instead */
        /*width: 550px;*/
    }
    <?php $css = ob_get_clean() ?>
    <?php $this->registerCss($css) ?>
</style>
