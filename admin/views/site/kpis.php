<?php


use common\assets\BackendAsset;
use common\components\extensions\DateRangeColumnInput;
use common\components\extensions\Select2;
use common\models\Maintenance;
use common\models\RepairRequest;
use common\models\Sector;
use common\models\Technician;
use common\widgets\dashboard\PanelBox;
use yii\base\DynamicModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dynamicModel DynamicModel */
/* @var $models RepairRequest[] */
/* @var $repairRequestsQuery \yii\db\ActiveQuery */

$this->title = Yii::t('app', 'KPIs');
$this->params['breadcrumbs'][] = $this->title;


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

<?php \common\assets\ChartJsAsset::register($this); ?>
<div class="quality-of-service">
    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => 'Quality Of Service',
                'icon'  => 'check',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <?= Html::beginForm(['site/kpis'], 'GET', ['class' => 'form-inline']) ?>
            <div class="form-group">
                <label class="display-block" for="sector-input">Date</label>
                <?= DateRangeColumnInput::widget([
                    'model'          => $dynamicModel,
                    'attribute_from' => '_s',
                    'attribute_to'   => '_e',
                    'allowClear'     => false
                ]) ?>
            </div>
            <div class="form-group" style="min-width: 200px">
                <label class="display-block" for="sector-input">Sector</label>
                <?= Select2::widget([
                    'id'            => 'sector-input',
                    'name'          => Html::getInputName($dynamicModel, 'sector'),
                    'value'         => $dynamicModel->sector,
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
            <div class="form-group">
                <label class="display-block" for="technician-input">Technician</label>
                <?= Html::dropDownList(Html::getInputName($dynamicModel, 'technician'), $dynamicModel->technician,
                    \common\models\users\Admin::techniciansKeyValList(),
                    ['class' => 'form-control', 'id' => 'technician-input', 'prompt' => 'All']) ?>
            </div>
            <div class="form-group">
                <label class="display-block" for="technician-input">&nbsp;</label>
                <button type="submit" class="btn btn-default">Filter</button>
            </div>
            <?= Html::endForm() ?>
            <br/>

            <?php $count = count($models);
            $avgResponseTime = 0;
            $avgInterventionTime = 0;
            ?>
            <?php
            $avgResponseTime = round((new Query())->select(["AVG(GREATEST(TIMESTAMPDIFF(SECOND, scheduled_at, arrived_at),0)) as total_seconds"])
                    ->from(['t' => $repairRequestsQuery])
                    ->scalar() / 60, 3);

            $avgInterventionTime = round((new Query())->select(["AVG(GREATEST(TIMESTAMPDIFF(SECOND, arrived_at, departed_at),0)) as total_seconds"])
                    ->from(['t' => $repairRequestsQuery])->scalar() / 60, 3);
            ?>
            <div class="clearfix"></div>
            <div class="col-md-6 text-center">
                <div class="GaugeMeter" title="<?= $avgResponseTime ?>"
                     data-text="<?= round($avgResponseTime, 0) ?>'"

                     data-size="250"
                     data-width="20"
                     data-percent="100"
                     data-label=" "
                     data-style="Arch"
                     data-stripe="4"
                     data-animationstep="3"

                     data-animate_text_colors="1"
                     data-animate_gauge_colors="1"
                     data-color="#388BFF"
                     data-back="#ffffff"
                     data-theme="Red-Gold-Green"
                >
                    <h3 class="text-center" style="margin: 0;position: absolute;bottom: 15px;width: 100%">
                        Response Time</h3>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <div class="GaugeMeter" title="<?= $avgInterventionTime ?>"
                     data-text="<?= round($avgInterventionTime, 0) ?>'"

                     data-size="250"
                     data-width="20"
                     data-percent="100"
                     data-label=" "
                     data-style="Arch"
                     data-stripe="4"
                     data-animationstep="3"

                     data-animate_text_colors="1"
                     data-animate_gauge_colors="1"
                     data-color="#388BFF"
                     data-back="#ffffff"
                     data-theme="Red-Gold-Green"
                >
                    <h3 class="text-center" style="margin: 0;position: absolute;bottom: 15px;width: 100%">
                        Intervention Time</h3>
                </div>
            </div>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<?php $this->registerJsFile(Yii::getAlias("@staticWeb/plugins/GaugeMeter.js"), ['depends' => BackendAsset::className()]) ?>

<div class="productivity">
    <div class="row equal-col-height">
        <div class="col-md-6">
            <?php $panel = PanelBox::begin([
                'title' => 'Productivity',
                'icon'  => 'area-chart',
                'color' => PanelBox::COLOR_GREEN
            ]);
            ?>

            <?= Html::beginForm(['site/kpis'], 'GET', ['class' => 'form-inline']) ?>
            <?php
            $period = Yii::$app->request->get("period") ? Yii::$app->request->get("period") : 'last-quarter';

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
                <label class="display-block" for="custom-range">Custom Range</label>
                <?= \kartik\date\DatePicker::widget([
                    'id'            => 'custom-range',
                    'name'          => "from_month",
                    'name2'         => "to_month",
                    'options'       => ['placeholder' => Yii::t('app', 'From Month')],
                    'options2'      => ['placeholder' => Yii::t('app', 'To Month')],
                    'value'         => $from_month,
                    'value2'        => $to_month,
                    'type'          => \kartik\date\DatePicker::TYPE_RANGE,
                    'pluginOptions' => [
                        'autoclose'   => true,
                        'startView'   => 'year',
                        'minViewMode' => 'months',
                        'format'      => 'yyyy-mm',
                        'endDate'     => date('Y-m', strtotime(date('Y-m') . '-01 -3 days')),
                    ]
                ]) ?>
            </div>
            <div class="form-group" style="min-width: 200px">
                <label class="display-block" for="sector-input">Sector</label>
                <?= Select2::widget([
                    'id'            => 'sector-input-2',
                    'name'          => Html::getInputName($dynamicModel, 'p_sector'),
                    'value'         => $dynamicModel->p_sector,
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
            <div class="form-group">
                <label class="display-block" for="technician-input">Technician</label>
                <?= Html::dropDownList(Html::getInputName($dynamicModel, 'p_technician'), $dynamicModel->p_technician,
                    \common\models\users\Admin::techniciansKeyValList(),
                    ['class' => 'form-control', 'id' => 'technician-input-2', 'prompt' => 'All']) ?>
            </div>
            <div class="form-group">
                <label class="display-block" for="technician-input">&nbsp;</label>
                <button type="submit" class="btn btn-default">Filter</button>
            </div>
            <?= Html::endForm() ?>
            <br/>
            <?php
            $data = Maintenance::find()
                ->select([
                    "COUNT(DISTINCT `technician_id`) as `t`",
                    "COUNT(*) as 'c'",
                    "DATE_FORMAT(`maintenance`.`completed_at`, '%Y_%m') AS 'year_month'"
                ])
                ->joinWith(['location', 'location.sector', 'technician'], false)
                ->where([
                    'AND',
                    [Maintenance::tableName() . '.status' => Maintenance::STATUS_COMPLETE],
                    [">=", Maintenance::tableName() . '.completed_at', "{$from_month_val} 00:00:00"],
                    ["<=", Maintenance::tableName() . '.completed_at', "{$to_month_val} 23:59:59"],
                ])
                ->andFilterWhere([Maintenance::tableName() . '.technician_id' => $dynamicModel->p_technician])
                ->andFilterWhere([Sector::tableName() . '.id' => $dynamicModel->p_sector])
                ->groupBy(['year_month'])
                ->orderBy(['year_month' => SORT_ASC])
                ->asArray()
                ->all();
            if (count($data) == 1) {
                // $data[] = $data[0];
            }
            //print_r($maintenances);
            ?>
            <?php
            $chartDataVisitsPerTechnician = [];
            $chartDataTechnicians = [];
            $chartDataVisitsCount = [];
            $chartLabels = [];
            $chartAverage = [];
            $total = 0;
            foreach ($data as $index => $datum) {
                $val = ceil($datum['c'] / $datum['t']);
                $total += $val;

                $chartDataVisitsPerTechnician[] = $val;
                $chartDataTechnicians[] = $datum['t'];
                $chartDataVisitsCount[] = $datum['c'];

                $exp = explode("_", $datum['year_month']);
                $labl = date("M Y", strtotime("{$exp[0]}-{$exp[1]}-15"));
                $chartLabels[] = $labl;//$datum['year_month'];
            }
            $avg = count($data) > 0 ? round($total / count($data), 1) : 0;
            foreach ($data as $index => $datum) {
                $chartAverage[] = $avg;
            }

            $d = \yii\helpers\Json::encode($chartDataVisitsPerTechnician);
            $dt = \yii\helpers\Json::encode($chartDataTechnicians);
            $dc = \yii\helpers\Json::encode($chartDataVisitsCount);
            $a = \yii\helpers\Json::encode($chartAverage);
            $l = \yii\helpers\Json::encode($chartLabels);
            ?>
            <canvas id="myChart" style="height: 280px;width:100%"></canvas>
            <script>
                <?php ob_start() ?>
                Chart.register(ChartDataLabels);
                Chart.register(window['chartjs-plugin-annotation']);

                function average(ctx) {
                    const values = ctx.chart.data.datasets[0].data;
                    var avg = values.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) / values.length;
                    console.log(">>", values, avg);
                    return avg;
                }

                const annotation = {
                    //yAxisID: 'B',
                    scaleID: 'yAxis1',
                    type: 'line',
                    borderColor: 'red',
                    borderDash: [6, 6],
                    borderDashOffset: 0,
                    borderWidth: 3,
                    label: {
                        enabled: true,
                        content: (ctx) => 'Avg: ' + average(ctx).toFixed(1),
                        position: 'end'
                    },
                    //scaleID: 'yAxis',
                    value: (ctx) => average(ctx)
                };

                var labels = <?= $l ?>;
                const config = {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                yAxisID: 'yAxis1',
                                type: 'line',
                                label: '# Visits / Tech',
                                data: <?= $d ?>,
                                backgroundColor: '#5dc61b',
                                borderColor: '#5dc61b',
                                fill: false,
                                hoverOffset: 4,
                                datalabels: {
                                    display: true,
                                    clamp: true,
                                    align: '-135',
                                    anchor: 'end'
                                }
                            },
                            {
                                yAxisID: 'yAxis1',
                                type: 'line',
                                label: '# Technicians',
                                data: <?= $dt ?>,
                                backgroundColor: '#c61b1b',
                                borderColor: '#c61b1b',
                                fill: false,
                                hoverOffset: 4,
                                datalabels: {
                                    display: true,
                                    clamp: true,
                                    align: '45',
                                    anchor: 'start'
                                }
                            },
                            {
                                yAxisID: 'yAxis',
                                type: 'bar',
                                barThickness: 20,
                                label: '# Visits / Month',
                                data: <?= $dc ?>,
                                backgroundColor: "#0d8be6",
                                fill: true,
                                hoverOffset: 4,
                                datalabels: {
                                    display: true,
                                    clamp: true,
                                    align: 'end',
                                    anchor: 'start'
                                }
                            },
                        ]
                    },
                    options: {
                        stacked: false,
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
                                grid: {display: false, ticks: false},
                                ticks: {
                                    // callback: function (value, index, ticks) {
                                    //     return labels[index].split('-')[2];
                                    // }
                                }
                            },
                            yAxis: {
                                type: 'linear',
                                display: true,
                                // stacked: true,
                                offset: true,
                                beginAtZero: true,
                                // grid line settings
                                grid: {
                                    drawOnChartArea: false, // only want the grid lines for one axis to show up
                                },
                                ticks: {
                                    display: false,
                                    suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
                                }
                            },
                            yAxis1: {
                                display: true,
                                // stacked: true,
                                offset: true,
                                beginAtZero: true,
                                type: 'linear',
                                position: 'right',
                                // grid line settings
                                grid: {
                                    drawOnChartArea: false, // only want the grid lines for one axis to show up
                                },
                                ticks: {
                                    display: false,
                                    suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
                                }
                            }
                        },
                        plugins: {
                            datalabels: {
                                backgroundColor: function (context) {
                                    return context.dataset.backgroundColor;
                                },
                                borderRadius: 4,
                                color: 'white',
                                font: {
                                    //weight: 'bold'
                                },
                                formatter: Math.round,
                                padding: 2
                            },
                            annotation: {
                                annotations: {
                                    annotation
                                }
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
            <?php if (false) { ?>
                <?php $uniquetechCounts = count($maintenances); ?>
                <?php $totalCompletedCounts = array_sum(ArrayHelper::getColumn($maintenances, 'c', false)); ?>
                <?php $gaugeValue = $uniquetechCounts > 0 ? ceil($totalCompletedCounts / $uniquetechCounts) : 0 ?>

                <div class="clearfix"></div>
                <div class="col-md-6 col-md-offset-3 text-center">
                    <div class="GaugeMeter"
                         data-text="<?= ceil($gaugeValue) ?>"

                         data-size="250"
                         data-width="20"
                         data-percent="100"
                         data-label=" "
                         data-style="Arch"
                         data-stripe="4"
                         data-animationstep="3"

                         data-animate_text_colors="1"
                         data-animate_gauge_colors="1"
                         data-color="#388BFF"
                         data-back="#ffffff"
                         data-theme="Red-Gold-Green"
                    >
                    </div>
                    <h3 class="text-center m-0"># Visits / Month</h3>
                </div>
            <?php } ?>
            <?php PanelBox::end() ?>
        </div>

        <?php if (false) { ?>
            <div class="col-md-6">
                <?php $panel = PanelBox::begin([
                    'title' => 'Productivity',
                    'icon'  => 'area-chart',
                    'color' => PanelBox::COLOR_GREEN
                ]);
                ?>

                <?= Html::beginForm(['site/kpis'], 'GET', ['class' => 'form-inline']) ?>
                <?php
                $period = Yii::$app->request->get("period") ? Yii::$app->request->get("period") : 'last-quarter';

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
                    <?= \kartik\date\DatePicker::widget([
                        'name'          => "from_month",
                        'name2'         => "to_month",
                        'options'       => ['placeholder' => Yii::t('app', 'From Month')],
                        'options2'      => ['placeholder' => Yii::t('app', 'To Month')],
                        'value'         => $from_month,
                        'value2'        => $to_month,
                        'type'          => \kartik\date\DatePicker::TYPE_RANGE,
                        'pluginOptions' => [
                            'autoclose'   => true,
                            'startView'   => 'year',
                            'minViewMode' => 'months',
                            'format'      => 'yyyy-mm',
                            'endDate'     => date('Y-m', strtotime(date('Y-m') . '-01 -3 days')),
                        ]
                    ]) ?>
                </div>
                <div class="form-group" style="min-width: 200px">
                    <label class="display-block" for="sector-input">Sector</label>
                    <?= Select2::widget([
                        'id'            => 'sector-input-2',
                        'name'          => Html::getInputName($dynamicModel, 'p_sector'),
                        'value'         => $dynamicModel->p_sector,
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
                    <label class="display-block" for="technician-input">Technician</label>
                    <?= Html::dropDownList(Html::getInputName($dynamicModel, 'p_technician'), $dynamicModel->p_technician,
                        ArrayHelper::map(Technician::find()->all(), 'id', 'name'),
                        ['class' => 'form-control', 'id' => 'technician-input-2', 'prompt' => 'All']) ?>
                </div>
                <div class="form-group">
                    <label class="display-block" for="technician-input">&nbsp;</label>
                    <button type="submit" class="btn btn-default">Filter</button>
                </div>
                <?= Html::endForm() ?>
                <br/>
                <?php
                $data = Maintenance::find()
                    ->select([
                        "COUNT(DISTINCT `technician_id`) as `t`",
                        "COUNT(*) as 'c'",
                        "DATE_FORMAT(`maintenance`.`completed_at`, '%Y_%m') AS 'year_month'"
                    ])
                    ->joinWith(['location', 'location.sector', 'technician'], false)
                    ->where([
                        'AND',
                        [Maintenance::tableName() . '.status' => Maintenance::STATUS_COMPLETE],
                        [">=", Maintenance::tableName() . '.completed_at', "{$from_month_val} 00:00:00"],
                        ["<=", Maintenance::tableName() . '.completed_at', "{$to_month_val} 23:59:59"],
                    ])
                    ->andFilterWhere([Maintenance::tableName() . '.technician_id' => $dynamicModel->technician])
                    ->andFilterWhere([Sector::tableName() . '.id' => $dynamicModel->sector])
                    ->groupBy(['year_month'])
                    ->orderBy(['year_month' => SORT_ASC])
                    ->asArray()
                    ->all();
                if (count($data) == 1) {
                    // $data[] = $data[0];
                }
                //print_r($maintenances);
                ?>
                <?php
                $chartData = [];
                $chartLabels = [];
                $chartAverage = [];
                $total = 0;
                foreach ($data as $index => $datum) {
                    $val = ceil($datum['c'] / $datum['t']);
                    $total += $val;
                    $chartData[] = $val;

                    $exp = explode("_", $datum['year_month']);
                    $labl = date("M Y", strtotime("{$exp[0]}-{$exp[1]}-15"));
                    $chartLabels[] = $labl;//$datum['year_month'];
                }
                $avg = count($data) > 0 ? round($total / count($data), 1) : 0;
                foreach ($data as $index => $datum) {
                    $chartAverage[] = $avg;
                }

                $d = \yii\helpers\Json::encode($chartData);
                $a = \yii\helpers\Json::encode($chartAverage);
                $l = \yii\helpers\Json::encode($chartLabels);
                ?>
                <canvas id="myChart" style="height: 280px;width:100%"></canvas>
                <script>
                    <?php ob_start() ?>
                    Chart.register(ChartDataLabels);
                    Chart.register(window['chartjs-plugin-annotation']);

                    function average(ctx) {
                        const values = ctx.chart.data.datasets[0].data;
                        var avg = values.reduce((a, b) => a + b, 0) / values.length;
                        console.log(">>", values, avg);
                        return avg;
                    }

                    const annotation = {
                        type: 'line',
                        borderColor: 'red',
                        borderDash: [6, 6],
                        borderDashOffset: 0,
                        borderWidth: 3,
                        label: {
                            enabled: true,
                            content: (ctx) => 'Average: ' + average(ctx).toFixed(2),
                            position: 'end'
                        },
                        scaleID: 'yAxis',
                        value: (ctx) => average(ctx)
                    };

                    var labels = <?= $l ?>;
                    const config = {
                        type: labels.length > 1 ? 'line' : 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                //{
                                //    type: 'line',
                                //    label: 'Average',
                                //    data: <?//= $a ?>//,
                                //    backgroundColor: '#c61b1b',
                                //    borderColor: '#c61b1b',
                                //    fill: false,
                                //    hoverOffset: 4,
                                //    datalabels: {
                                //        display: false
                                //    //     align: 'start',
                                //    //     anchor: 'start'
                                //    }
                                //},
                                {
                                    type: labels.length > 1 ? 'line' : 'bar',
                                    label: '# Visits / Month',
                                    data: <?= $d ?>,
                                    backgroundColor: "#0d8be6",
                                    fill: true,
                                    hoverOffset: 4,
                                    datalabels: {
                                        display: true,
                                        align: 'end',
                                        anchor: 'start'
                                    }
                                },
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            //aspectRatio: 16 / 4,
                            scales: {
                                xAxis: {
                                    stacked: true,
                                    // offset: true,
                                    grid: {display: false},
                                    ticks: {
                                        // callback: function (value, index, ticks) {
                                        //     return labels[index].split('-')[2];
                                        // }
                                    }
                                },
                                yAxis: {
                                    display: true,
                                    stacked: true,
                                    offset: true,
                                    beginAtZero: true,
                                    grid: {display: false},
                                    ticks: {
                                        suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
                                    }
                                }
                            },
                            plugins: {
                                datalabels: {
                                    backgroundColor: function (context) {
                                        return "#eee";
                                        context.dataset.backgroundColor;
                                    },
                                    borderRadius: 4,
                                    color: 'black',
                                    font: {
                                        weight: 'bold'
                                    },
                                },
                                annotation: {
                                    annotations: {
                                        annotation
                                    }
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
        <?php } ?>
        <div class="col-md-6">
            <?php $panel = PanelBox::begin([
                'title' => 'Customer Satisfaction',
                'icon'  => 'star',
                'color' => PanelBox::COLOR_GREEN
            ]);
            ?>

            <?= Html::beginForm(['site/kpis'], 'GET', ['class' => 'form-inline']) ?>
            <div class="form-group">
                <label class="display-block" for="sector-input">Date</label>
                <?= DateRangeColumnInput::widget([
                    'model'          => $dynamicModel,
                    'attribute_from' => 's_s',
                    'attribute_to'   => 's_e',
                    'allowClear'     => false
                ]) ?>
            </div>
            <div class="form-group" style="min-width: 200px">
                <label class="display-block" for="sector-input-s">Sector</label>
                <?= Select2::widget([
                    'id'            => 'sector-input-s',
                    'name'          => Html::getInputName($dynamicModel, 's_sector'),
                    'value'         => $dynamicModel->s_sector,
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
            <div class="form-group">
                <label class="display-block" for="technician-input">Technician</label>
                <?= Html::dropDownList(Html::getInputName($dynamicModel, 's_technician'), $dynamicModel->s_technician,
                    \common\models\users\Admin::techniciansKeyValList(),
                    ['class' => 'form-control', 'id' => 'technician-input', 'prompt' => 'All']) ?>
            </div>
            <div class="form-group">
                <label class="display-block" for="technician-input">&nbsp;</label>
                <button type="submit" class="btn btn-default">Filter</button>
            </div>
            <?= Html::endForm() ?>
            <br/>
            <?php
            $repairRequestsRating = RepairRequest::find()
                ->joinWith(['equipment','equipment.location','equipment.location.sector'])
                ->where([
                    'AND',
                    //[RepairRequest::tableName() . '.type' => RepairRequest::TYPE_REQUEST],
                    ['>=', RepairRequest::tableName() . '.departed_at', $dynamicModel->s_s . ' 00:00:00'],
                    ['<=', RepairRequest::tableName() . '.departed_at', $dynamicModel->s_e . ' 23:59:59'],
                ])
                ->andFilterWhere([RepairRequest::tableName() . '.technician_id' => $dynamicModel->s_technician])
                ->andFilterWhere([Sector::tableName() . '.id' => $dynamicModel->s_sector])
                ->average('rating');
            $repairRequestsRatingCount = RepairRequest::find()
                ->joinWith(['equipment','equipment.location','equipment.location.sector'])
                ->where([
                    'AND',
                    //[RepairRequest::tableName() . '.type' => RepairRequest::TYPE_REQUEST],
                    ['>=', RepairRequest::tableName() . '.departed_at', $dynamicModel->s_s . ' 00:00:00'],
                    ['<=', RepairRequest::tableName() . '.departed_at', $dynamicModel->s_e . ' 23:59:59'],
                ])
                ->andFilterWhere([RepairRequest::tableName() . '.technician_id' => $dynamicModel->s_technician])
                ->andFilterWhere([Sector::tableName() . '.id' => $dynamicModel->s_sector])
                ->andWhere(['IS NOT', 'rating', null])
                ->count();
            ?>
            <div class="clearfix"></div>
            <div class="col-md-12 text-center">
                <div class="GaugeMeter"
                     data-text="<?= round($repairRequestsRating, 1) ?><sup style='font-size: 20px'><sup class='fa fa-star'></sup><sup>"

                     data-size="250"
                     data-width="20"
                     data-percent="100"
                     data-label=" "
                     data-style="Arch"
                     data-stripe="4"
                     data-animationstep="3"

                     data-animate_text_colors="1"
                     data-animate_gauge_colors="1"
                     data-color="#388BFF"
                     data-back="#ffffff"
                     data-theme="Red-Gold-Green"
                >
                    <h3 class="text-center" style="margin: 0;position: absolute;bottom: 15px;width: 100%">
                        # Services Rated = <?= $repairRequestsRatingCount ?></h3>
                </div>
            </div>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<?php if (false) { ?>
    <script>
        <?php ob_start() ?>
        console.log(
            <?= $uniquetechCounts ?>,
            <?= $totalCompletedCounts ?>,
            <?= $gaugeValue ?>,
        );
        console.log(
            <?= \yii\helpers\Json::encode($maintenances) ?>
        )
        <?php $js = ob_get_clean() ?>
        <?php $this->registerJs($js) ?>
    </script>
<?php } ?>
<script>
    <?php ob_start() ?>
    $(".GaugeMeter").gaugeMeter();
    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>
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
    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>
<style>
    <?php ob_start() ?>
    .GaugeMeter {
        position: Relative;
        text-align: Center;
        overflow: Hidden;
        cursor: Default;
        display: inline-block;
    }

    .GaugeMeter SPAN, .GaugeMeter B {
        width: 54%;
        position: Absolute;
        text-align: Center;
        display: Inline-Block;
        color: RGBa(0, 0, 0, .8);
        font-weight: 100;
        font-family: "Open Sans", Arial;
        overflow: Hidden;
        white-space: NoWrap;
        text-overflow: Ellipsis;
        margin: 0 23%;
    }

    .GaugeMeter B {
        width: 80%;
        margin: 0 10%;
    }

    .GaugeMeter S, .GaugeMeter U {
        text-decoration: None;
        font-size: .60em;
        font-weight: 200;
        opacity: .6;
    }

    .GaugeMeter B {
        color: #000;
        font-weight: 200;
        opacity: .8;
    }

    .equal-col-height {
        display: flex;
        align-items: stretch;
        flex-wrap: wrap;
    }

    .equal-col-height > div {
        flex-grow: 1;
    }

    .box.box-success {
        height: 100%;
    }

    <?php $css = ob_get_clean() ?>
    <?php $this->registerCss($css) ?>
</style>
