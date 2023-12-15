<?php

use common\models\BarcodeScan;
use common\models\Equipment;
use common\models\EquipmentMaintenanceBarcode;
use common\models\Location;
use common\models\Maintenance;
use common\widgets\dashboard\PanelBox;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;

/* @var $this View */
/* @var $type string */
/* @var $title string */
/* @var $codes array */

$id = "chart_" . rand(1000000, 9999999);

$ts1 = strtotime($from_month_val);
$ts2 = strtotime($to_month_val);

$year1 = date('Y', $ts1);
$year2 = date('Y', $ts2);
$month1 = date('m', $ts1);
$month2 = date('m', $ts2);
$numberOfMonths = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;

//echo "{$from_month_val} - {$to_month_val} = {$diff} Months";
$sector = Yii::$app->request->get('sector');
if(empty($sector)) {
    $sector = \common\models\users\Admin::activeSectorsIds();
}
$sector = array_intersect($sector ?: [], \common\models\users\Admin::activeSectorsIds());
$technician = Yii::$app->request->get("technician");

$barcodesLocationsMap = \common\models\EquipmentMaintenanceBarcode::getBarcodeFullLocationNames();
?>


<?php
$twoTimesBarcodes = ArrayHelper::map(EquipmentMaintenanceBarcode::find()
    ->select([EquipmentMaintenanceBarcode::tableName() . '.code', 'count(*) as count'])
    ->joinWith(['equipment', 'equipment.location', 'equipment.maintenances'], false)
    ->where([
        'AND',
        [">=", Maintenance::tableName() . '.created_at', "{$from_month_val} 00:00:00"],
        ["<=", Maintenance::tableName() . '.created_at', "{$to_month_val} 23:59:59"],
    ])
    ->andFilterWhere([Equipment::tableName() . '.manufacturer' => Equipment::MANUFACTURER_THIRD_PARTY])
    ->andFilterWhere([Equipment::tableName() . '.equipment_type' => $type])
    //->andFilterWhere([Equipment::tableName() . '.actually_in' => true])
    ->andFilterWhere([EquipmentMaintenanceBarcode::tableName() . '.code' => $codes])
    ->andFilterWhere([Location::tableName() . '.sector_id' => $sector])
    ->groupBy([EquipmentMaintenanceBarcode::tableName() . '.code'])
    ->asArray()
    ->all(), "code", "count");

$oneTimeBarcodes = ArrayHelper::map(EquipmentMaintenanceBarcode::find()
    ->select([EquipmentMaintenanceBarcode::tableName() . '.code', 'count(*) as count'])
    ->joinWith(['equipment', 'equipment.location', 'equipment.maintenances'], false)
    ->where([
        'AND',
        [">=", Maintenance::tableName() . '.created_at', "{$from_month_val} 00:00:00"],
        ["<=", Maintenance::tableName() . '.created_at', "{$to_month_val} 23:59:59"],
    ])
    ->andFilterWhere([Equipment::tableName() . '.manufacturer' => Equipment::MANUFACTURER_EMAINTAIN])
    ->andFilterWhere([Equipment::tableName() . '.equipment_type' => $type])
    //->andFilterWhere([Equipment::tableName() . '.actually_in' => true])
    ->andFilterWhere([EquipmentMaintenanceBarcode::tableName() . '.code' => $codes])
    ->andFilterWhere([Location::tableName() . '.sector_id' => $sector])
    ->groupBy([EquipmentMaintenanceBarcode::tableName() . '.code'])
    ->asArray()
    ->all(), "code", "count");

$scannedBarcodes = ArrayHelper::map(BarcodeScan::find()
    ->select([EquipmentMaintenanceBarcode::tableName() . '.code', 'count(*) as count'])
    ->joinWith(['maintenance', 'maintenance.equipment', 'maintenance.equipment.location', 'barcode'], false)
    ->where([
        'AND',
        [">=", BarcodeScan::tableName() . '.created_at', "{$from_month_val} 00:00:00"],
        ["<=", BarcodeScan::tableName() . '.created_at', "{$to_month_val} 23:59:59"],
    ])
    ->andFilterWhere([Equipment::tableName() . '.equipment_type' => $type])
    ->andFilterWhere([EquipmentMaintenanceBarcode::tableName() . '.code' => $codes])
    ->andFilterWhere([Location::tableName() . '.sector_id' => $sector])
    ->groupBy([EquipmentMaintenanceBarcode::tableName() . '.code'])
    ->asArray()
    ->all(), "code", "count");

$data = [];
foreach ($twoTimesBarcodes as $code => $count) {
    $data[$code] = [
        'code'    => $code,
        'total'   => $count,// * 2),// * $numberOfMonths,
        'scanned' => 0,
        'label'   => EquipmentMaintenanceBarcode::codeDescMap($code),
    ];
}
foreach ($oneTimeBarcodes as $code => $count) {
    if (empty($data[$code])) {
        $data[$code] = [
            'code'    => $code,
            'total'   => 0,
            'scanned' => 0,
            'label'   => EquipmentMaintenanceBarcode::codeDescMap($code),
        ];
    }
    $data[$code]['total'] += $count;// * $numberOfMonths;
}
foreach ($scannedBarcodes as $code => $count) {
    if (empty($data[$code])) {
        $data[$code] = [
            'code'    => $code,
            'total'   => 0,
            'scanned' => 0,
            'label'   => EquipmentMaintenanceBarcode::codeDescMap($code),
        ];
    }
    $data[$code]['scanned'] = $count;
}

$noKeyData = [];
foreach ($data as $code => $datum) {
    $datum['percent'] = ceil($datum['scanned'] * 100 / $datum['total']);

    $lbls = $barcodesLocationsMap[$datum['code']];
//    if (count($lbls) > 6) {
//        $lbls = array_slice($lbls, 0, 6);
//        $lbls[] = "....";
//    }
    $lbl = implode("\n", $lbls);
    $datum['desc'] = $lbl;
    $noKeyData[] = $datum;
}
ArrayHelper::multisort($noKeyData, 'code', SORT_ASC);
$total = array_sum(ArrayHelper::getColumn($noKeyData, 'total', false));
$scanned = array_sum(ArrayHelper::getColumn($noKeyData, 'scanned', false));
?>
<?php $panel = PanelBox::begin([
    'title' => $title . " [ {$scanned} / {$total} ]",
    'icon'  => '',
    'body'  => false,
    'solid' => false,
    //'color' => PanelBox::COLOR_RED
]);
?>
<?php
$chartDataPercent = [];
$chartDataTotal = [];
$chartDataScanned = [];
$chartDataLabels = [];
foreach ($noKeyData as $code => $datum) {
    $chartDataTotal[] = $datum['total'];
    $chartDataScanned[] = $datum['scanned'];
    $chartDataPercent[] = ceil($datum['scanned'] * 100 / $datum['total']);
    $chartDataLabels[] = $datum['code'] . "\n" . $datum['label'];// . "\n" . $datum['desc'];

}
$dt = Json::encode($chartDataTotal);
$ds = Json::encode($chartDataScanned);
$dp = Json::encode($chartDataPercent);
$l = Json::encode($chartDataLabels);
?>
<canvas id="<?= $id ?>" style="height: 280px;width:100%"></canvas>
<div class="table-responsive">
    <table class="table table-condensed table-striped table-bordered table-layout-fixed">
        <thead>
        <tr>
            <!--            <th>Code</th>-->
            <?php foreach ($noKeyData as $code => $datum) { ?>
                <td>
                    <div data-html="true" data-toggle="tooltip" title="<?= $datum['code'] ?><br><?= $datum['label'] ?><br><?= str_replace("\n","<br>",$datum['desc']) ?>">
                        <?= $datum['label'] ?>
                    </div>
                </td>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <!--            <th>Total</th>-->
            <?php foreach ($noKeyData as $code => $datum) { ?>
                <td><?= $datum['total'] ?></td>
            <?php } ?>
        </tr>
        <tr>
            <!--            <th>Scanned</th>-->
            <?php foreach ($noKeyData as $code => $datum) { ?>
                <td><?= $datum['scanned'] ?></td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
</div>

<script>
    <?php ob_start() ?>
    (() => {
        Chart.register(ChartDataLabels);
        var dt = <?= $dt ?>;
        var ds = <?= $ds ?>;
        var footer = (tooltipItems) => {
            let total = 0;
            let scanned = 0;
            tooltipItems.forEach(function (tooltipItem) {
                total = dt[tooltipItem.dataIndex];
                scanned = ds[tooltipItem.dataIndex];
            });
            return scanned + ' / ' + total;
            return 'Total: ' + total + "\r\n" +
                'Scanned: ' + scanned;
        };

        var labels = <?= $l ?>;
        var config = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    //{
                    //    display: false,
                    //    type: 'bar',
                    //    label: 'Total',
                    //    data: <?//= $dt ?>//,
                    //    backgroundColor: "#ff0000",
                    //    datalabels: {
                    //        display: true,
                    //        align: 'bottom',
                    //        anchor: 'end'
                    //    }
                    //},
                    {
                        type: 'bar',
                        label: '% Scanned',
                        data: <?= $dp ?>,
                        backgroundColor: "#0d8be6",//"<?= !empty($color) ? $color : "#0d8be6" ?>",//,
                        //hoverOffset: 4,
                        barThickness: 20,
                        datalabels: {
                            display: true,
                            align: 'end',
                            anchor: 'end'
                        }
                    },
                ]
            },
            options: {
                layout: {
                    padding: {
                        top: 23
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },

                responsive: true,
                maintainAspectRatio: true,
                //aspectRatio: 16 / 4,
                scales: {
                    xAxis: {
                        stacked: true,
                        // offset: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            display: false,
                            // callback: function (value, index, ticks) {
                            //     return labels[index].split('-')[2];
                            // }
                        }
                    },
                    yAxis: {
                        display: true,
                        stacked: true,
                        offset: false,
                        beginAtZero: true,
                        max: 100,
                        min: 0,
                        grid: {
                            display: false
                        },
                        ticks: {
                            display: false,
                            suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
                            suggestedMax: 100,
                        }
                    }
                },
                plugins: {

                    tooltip: {
                        callbacks: {
                            footer: footer,
                        }
                    },
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        backgroundColor: function (context) {
                            return "#fff";
                            context.dataset.backgroundColor;
                        },
                        formatter: function (value, context) {
                            return value + "%";//context.chart.data.labels[context.dataIndex];
                        },
                        borderRadius: 4,
                        color: 'black',
                        font: {
                            weight: 'bold'
                        },
                    },
                },
            }
        };
        //console.log(config);
        var myChart = new Chart(
            document.getElementById('<?= $id ?>'),
            config
        );
    })();
    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>
<?php PanelBox::end() ?>
<style>
    <?php ob_start() ?>
    .table-responsive {
        padding-left: 7px;
    }

    .table-layout-fixed {
        table-layout: fixed;
        margin: 0;
    }

    .table-layout-fixed td {
        /*height: 60px;*/
        /*max-height: 60px;*/
        /*font-size: 12px;*/
        vertical-align: top !important;
        word-break: break-word;
        overflow: hidden;
        white-space: nowrap;
        padding: 1px;
        text-align: center;
    }

    <?php $css = ob_get_clean() ?>
    <?php $this->registerCss($css, [], "table-styles") ?>
</style>
