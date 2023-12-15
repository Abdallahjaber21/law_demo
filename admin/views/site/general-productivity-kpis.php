<?php


use common\assets\ChartJsAsset;
use common\components\extensions\Select2;
use common\config\includes\P;
use common\models\MaintenanceVisit;
use common\models\RepairRequest;
use common\models\Sector;
use common\models\Technician;
use common\models\WorkingHours;
use common\widgets\dashboard\PanelBox;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/* @var $this View */

$this->title = Yii::t('app', 'General Productivity');
$this->params['breadcrumbs'][] = $this->title;

ChartJsAsset::register($this);

$technician = Yii::$app->request->get('technician');
$sector = Yii::$app->request->get('sector');

$year = date("Y");
$years = [];
$months = [];
foreach (range($year - 1, $year) as $index => $item) {
    $years[$item] = $item;
}
foreach (range(1, 12) as $index => $item) {
    $months[$item] = $item;
}
$selectedYear = Yii::$app->request->get('year') ?: date("Y");
$selectedMonth = Yii::$app->request->get('month') ?: date("n");
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);

$m = substr("0{$selectedMonth}", -2);
$startOfMonth = "{$selectedYear}-{$m}-01";
$endofMonth = "{$selectedYear}-{$m}-{$daysInMonth}";
?>
<?php

$workingHours = WorkingHours::find()
    ->where(['year_month' => "{$selectedYear}-{$m}"])
    ->one();
if (empty($workingHours)) {
    $workingHours = new WorkingHours(['year_month' => "{$selectedYear}-{$m}"]);
}
?>

<?php $repairs = RepairRequest::find()
    ->select([
        'technician_id',
        //                    "TIMEDIFF(informed_at, departed_at)",
        //                    "TIMESTAMPDIFF(SECOND, informed_at, departed_at)",
        "SUM(TIMESTAMPDIFF(SECOND, informed_at, departed_at)) as total_minutes"
    ])
    ->joinWith(['equipment', 'equipment.location', 'equipment.location.sector', 'technician'], false)
    ->where([
        'AND',
        [RepairRequest::tableName() . '.status' => [RepairRequest::STATUS_COMPLETED, RepairRequest::STATUS_COMPLETED]],
        [">=", RepairRequest::tableName() . '.completed_at', "{$startOfMonth} 00:00:00"],
        ["<=", RepairRequest::tableName() . '.completed_at', "{$endofMonth} 23:59:59"],
    ])
    ->andFilterWhere([Sector::tableName() . '.id' => $sector])
    ->groupBy(['technician_id'])
    ->asArray()
    ->all();
$repairTimes = ArrayHelper::map($repairs, 'technician_id', 'total_minutes');

$visits = MaintenanceVisit::find()
    ->select([
        'technician_id',
        "SUM(TIMESTAMPDIFF(SECOND, checked_in, checked_out)) as total_minutes"
    ])
    ->joinWith(['location', 'location.sector', 'technician'], false)
    ->where([
        'AND',
        [MaintenanceVisit::tableName() . '.status' => MaintenanceVisit::STATUS_COMPLETED],
        [">=", MaintenanceVisit::tableName() . '.checked_out', "{$startOfMonth} 00:00:00"],
        ["<=", MaintenanceVisit::tableName() . '.checked_out', "{$endofMonth} 23:59:59"],
    ])
    ->andFilterWhere([Sector::tableName() . '.id' => $sector])
    ->groupBy(['technician_id'])
    ->asArray()
    ->all();
$visitsTimes = ArrayHelper::map($visits, 'technician_id', 'total_minutes');

$technicians = ArrayHelper::map(Technician::find()
    ->select(['id', 'name'])
    ->orderBy(['name' => SORT_ASC])
    ->all(), 'id', 'name');

$totalSeconds = 0;
$techniciansIncluded = 0;
foreach ($technicians as $id => $name) {
    $totalTime = @$repairTimes[$id] + @$visitsTimes[$id];
    if (empty($totalTime)) {
        continue;
    }
    $techniciansIncluded++;
    $totalSeconds += $totalTime;
}
?>
<div class="qr-kpis">
    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'dashboard',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <?= Html::beginForm(['site/general-productivity-kpis'], 'GET', ['class' => 'form-inline']) ?>

            <div class="form-group">
                <label class="display-block" for="technician-input">Year</label>
                <?= Html::dropDownList(
                    'year',
                    $selectedYear,
                    $years,
                    ['class' => 'form-control', 'id' => 'year-input']
                ) ?>
            </div>
            <div class="form-group">
                <label class="display-block" for="technician-input">Month</label>
                <?= Html::dropDownList(
                    'month',
                    $selectedMonth,
                    $months,
                    ['class' => 'form-control', 'id' => 'month-input']
                ) ?>
            </div>
            <div class="form-group hidden" style="min-width: 200px">
                <label class="display-block" for="sector-input">Sector</label>
                <?= Select2::widget([
                    'id'            => 'sector-input-2',
                    'name'          => 'sector',
                    'value'         => $sector,
                    'data'          => ArrayHelper::map(Sector::find()->orderBy(['code' => SORT_ASC])->all(), 'id', 'code'),
                    'pluginOptions' => [
                        'multiple'   => true,
                        'allowClear' => true
                    ],
                    'options'       => [
                        'placeholder' => 'Select Sectors',
                    ],
                ]) ?>
            </div>
            <div class="form-group">
                <label class="display-block" for="technician-input">&nbsp;</label>
                <button type="submit" class="btn btn-default">Filter</button>
            </div>
            <?= Html::endForm() ?>
            <br />

            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="bg-success">
                                <th>Total Working Hours</th>
                                <th>
                                    <?php if (!$workingHours->isNewRecord) { ?>
                                        <?= round($workingHours->total_hours, 1) ?> Hours
                                    <?php } else { ?>
                                        No Working hours specified for the selected month
                                        <?php if (P::c(P::MISC_MANAGE_WORKING_HOURS)) { ?>
                                            <?= Html::a("Add Working Hours", ['working-hours/create', 'year_month' => "{$selectedYear}-{$m}"], [
                                                'class' => 'btn btn-info btn-block btn-flat btn-xs'
                                            ]) ?>
                                        <?php } ?>
                                    <?php } ?>
                                </th>
                            </tr>
                            <tr class="bg-warning">
                                <th>Actual Avg. Working Hours</th>
                                <?php if ($techniciansIncluded > 0) { ?>
                                    <th><?= round(($totalSeconds / 3600) / $techniciansIncluded, 1) ?> Hours</th>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php PanelBox::end() ?>
        </div>

        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => "General Productivity Per Technician",
                'icon'  => 'dashboard',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php
            //
            //            $dataThisMonth = Maintenance::find()
            //                ->select(['DATE(completed_at) as c', 'count(*) as daily_count'])
            //                ->joinWith(['location', 'location.sector', 'technician'], false)
            //                ->where([
            //                    'AND',
            //                    [Maintenance::tableName() . '.status' => Maintenance::STATUS_COMPLETE],
            //                    [">=", Maintenance::tableName() . '.completed_at', "{$startOfMonth} 00:00:00"],
            //                    ["<=", Maintenance::tableName() . '.completed_at', "{$endofMonth} 23:59:59"],
            //                ])
            //                ->andFilterWhere(['year' => $thiYear])
            //                ->andFilterWhere(['month' => $thisMonth])
            //                ->andFilterWhere([Maintenance::tableName() . '.technician_id' => $technician])
            //                ->andFilterWhere([Sector::tableName() . '.id' => $sector])
            //                ->groupBy(['c'])
            //                //->orderBy(['completed_at'=>SORT_ASC])
            //                ->asArray()
            //                ->all();
            ?>

            <?php
            $colors = ['#f2d908', '#9de61e', '#0d8be6', '#c61b1b', '#e26f08', '#8d35d1'];
            $labels = [];
            $chartData = [];
            $fill = [];

            $chartData[] = round($workingHours->total_hours, 1);
            $labels[] = "[[Total Working Hours]]";
            $fill[] = '#ff3344';
            foreach ($technicians as $id => $name) {
                $totalTime = @$repairTimes[$id] + @$visitsTimes[$id];
                if (empty($totalTime)) {
                    continue;
                }
                $chartData[] = round($totalTime / 3600, 1);
                $labels[] = $name;
                $fill[] = '#0d8be6';
            }
            $l = Json::encode($labels);
            $d = Json::encode($chartData);
            $f = Json::encode($fill);
            ?>
            <canvas id="myChart" height="300"></canvas>

            <script>
                <?php ob_start() ?>
                Chart.register(ChartDataLabels);
                var labels = <?= $l ?>;
                const config = {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            type: 'bar',
                            label: 'Working hours',
                            data: <?= $d ?>,
                            backgroundColor: <?= $f ?>,
                            hoverOffset: 4,
                            datalabels: {
                                display: true,
                                align: 'end',
                                anchor: 'end'
                            }
                        }, ]
                    },
                    options: {
                        layout: {
                            padding: {
                                right: 30
                            }
                        },
                        indexAxis: 'y',
                        responsive: true,
                        // aspectRatio: 16 / 4,
                        scales: {
                            xAxis: {
                                stacked: true,
                                offset: true,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    callback: function(value, index, ticks) {
                                        return labels[index].split('-')[2];
                                    }
                                }
                            },
                            yAxis: {
                                display: true,
                                stacked: true,
                                offset: true,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    suggestedMin: 0, // minimum will be 0, unless there is a lower value.
                                    // OR //
                                    beginAtZero: true // minimum value will be 0.
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            datalabels: {
                                // display: function(context) {
                                //console.log(context);
                                // return totals[context.dataIndex] !== 0; // or >= 1 or ...
                                // },
                                backgroundColor: function(context) {
                                    return "#ffffff00";
                                    context.dataset.backgroundColor;
                                },
                                borderRadius: 4,
                                color: 'black',
                                font: {
                                    size: '11px',
                                    weight: 'bold',
                                },
                                //formatter: Math.round,
                                //padding: 6
                            }
                        },
                    }
                };
                console.log(config);
                var myChart = new Chart(
                    document.getElementById('myChart'),
                    config
                );

                <?php $js = ob_get_clean() ?>
                <?php $this->registerJs($js) ?>
            </script>

            <?php PanelBox::end() ?>
        </div>

    </div>
</div>

<script>
    <?php ob_start() ?>
    $("#period-input").change(function() {
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