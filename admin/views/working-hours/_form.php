<?php

use common\models\WorkingHours;
use common\widgets\dashboard\PanelBox;
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WorkingHours */
/* @var $form ActiveForm */

$yearMonth = Yii::$app->request->get("year_month");
$starts = $ends = $lunchs = $offs = [];
if (!$model->isNewRecord) {
    $yearMonth = $model->year_month;
    $model->holidays = !empty($model->holidays) && is_array($model->holidays) ? implode(",", $model->holidays) : "";
    $starts = $model->daily_hours['starts'];
    $ends = $model->daily_hours['ends'];
    $lunchs = $model->daily_hours['lunchs'];
    $offs = $model->daily_hours['offs'];
} else {
    $model->year_month = $yearMonth;
    $model->holidays = !empty($model->holidays) ? implode(",", $model->holidays) : "";
}
?>
<?php if (empty($yearMonth)) { ?>
    <?php $existingMonths = ArrayHelper::getColumn(WorkingHours::find()->select(['year_month'])->asArray()->all(), 'year_month', false) ?>
    <script>
        window.existingMonths = <?= Json::encode($existingMonths) ?>;
    </script>
    <div class="working-hours-form">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <?php $form = ActiveForm::begin([
                    'action' => ['create'],
                    'method' => 'GET',
                    'id'     => 'month-form'
                ]); ?>
                <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode($this->title),
                    'color' => PanelBox::COLOR_GRAY
                ]);
                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'year_month')->widget(DatePicker::className(), [
                            'options'       => [
                                'name'        => 'year_month',
                                'placeholder' => Yii::t('app', 'Select Month')
                            ],
                            'type'          => DatePicker::TYPE_INLINE,
                            'pluginOptions' => [
                                'autoclose'       => true,
                                'startView'       => 'year',
                                'minViewMode'     => 'months',
                                'format'          => 'yyyy-mm',
                                'startDate'       => "2022-01",
                                "beforeShowMonth" => new JsExpression("function(date) {
                                var ym = date.getFullYear()+'-'+ ('0'+ (date.getMonth() + 1)).substr(-2);
                                //console.log(ym, window.existingMonths, window.existingMonths.includes(ym));
                                return !window.existingMonths.includes(ym);
                                }")
                                //'endDate'     => date('Y-m', strtotime(date('Y-m') . '-01 -3 days')),
                            ]
                        ])->label("Select Month"); ?>
                    </div>
                </div>
                <?php PanelBox::end() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <script>
        <?php ob_start() ?>
        $("#<?= Html::getInputId($model, 'year_month') ?>").on("change", () => {
            $('#month-form').submit();
        });

        <?php $js = ob_get_clean() ?>
        <?php $this->registerJs($js) ?>
    </script>
<?php } else { ?>
    <?php
    $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    list($year, $month) = explode("-", $model->year_month);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $startDate = "{$model->year_month}-01";
    $endDate = "{$model->year_month}-{$daysInMonth}";

    ?>
    <div class="working-hours-form">
        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(); ?>
                <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode($this->title),
                    'color' => PanelBox::COLOR_GRAY
                ]);
                ?>
                <div class="row">
                    <div class="hidden">
                        <?= $form->field($model, 'year_month')->hiddenInput([
                            'name' => 'year_month'
                        ]) ?>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <?= $form->field($model, 'holidays')->widget(DatePicker::className(), [
                            'options'       => [
                                'name'        => 'holidays',
                                'placeholder' => Yii::t('app', 'Select Holidays')
                            ],
                            'type'          => DatePicker::TYPE_INLINE,
                            'pluginOptions' => [
                                'autoclose'          => true,
                                'startView'          => 'month',
                                'minViewMode'        => 'days',
                                'format'             => 'yyyy-mm-dd',
                                'weekStart'          => 1,
                                'multidate'          => true,
                                'multidateSeparator' => ',',
                                'startDate'          => $startDate,
                                'endDate'            => $endDate,
                            ]
                        ])->label("Select Holidays") ?>
                    </div>
                    <div class="col-sm-12 col-md-8 col-lg-9">
                        <label class="control-label">Select Daily working hours</label>
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <?php for ($i = 0; $i < 7; $i++) { ?>
                                    <th><?= $days[$i] ?></th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th>Start</th>
                                <?php for ($i = 0; $i < 7; $i++) { ?>
                                    <th class="text-center">
                                        <input type="text" id="start-<?= $i ?>" name="start[<?= $i ?>]" class="start-timepicker" value="<?= @$starts[$i] ?>" data-value="<?= @$starts[$i] ?>"/>
                                    </th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <th>End</th>
                                <?php for ($i = 0; $i < 7; $i++) { ?>
                                    <th class="text-center">
                                        <input type="text" id="end-<?= $i ?>" name="end[<?= $i ?>]" class="end-timepicker" value="<?= @$ends[$i] ?>" data-value="<?= @$ends[$i] ?>"/>
                                    </th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <th>Lunch</th>
                                <?php for ($i = 0; $i < 7; $i++) { ?>
                                    <th class="text-center">
                                        <input type="text" id="lunch-<?= $i ?>" name="lunch[<?= $i ?>]" class="lunch-timepicker" value="<?= @$lunchs[$i] ?>" data-value="<?= @$lunchs[$i] ?>"/>
                                    </th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <th>OFF</th>
                                <?php for ($i = 0; $i < 7; $i++) { ?>
                                    <th class="text-center">
                                        <input type="checkbox" name="off[<?= $i ?>]" data-i="<?= $i ?>" class="off-checkbox" <?= @$offs[$i] ? 'checked' : '' ?>/>
                                    </th>
                                <?php } ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group text-center">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
                </div>

                <?php PanelBox::end() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <script>
        <?php ob_start() ?>
        $('.start-timepicker').timepicker({
            timeFormat: 'H:mm',
            interval: 15,
            minTime: '6',
            maxTime: '9',
            defaultTime: <?= !$model->isNewRecord ? 'null' : '"7:30 am"' ?>,
            //startTime: '10:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });
        $('.end-timepicker').timepicker({
            timeFormat: 'H:mm',
            interval: 15,
            minTime: '15',
            maxTime: '19',
            defaultTime: <?= !$model->isNewRecord ? 'null' : '"17:30"' ?>,
            //startTime: '10:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });
        $('.lunch-timepicker').timepicker({
            timeFormat: 'H:mm',
            interval: 15,
            minTime: '0',
            maxTime: '2',
            defaultTime: <?= !$model->isNewRecord ? 'null' : '"0:30"' ?>,
            //startTime: '10:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });
        $(".off-checkbox").on("change", (e) => {
            var i = $(e.target).data("i");
            console.log(i)
            if ($(e.target).is(":checked")) {
                $("#start-" + i).attr('disabled', true).val('');
                $("#end-" + i).attr('disabled', true).val('');
                $("#lunch-" + i).attr('disabled', true).val('');
            } else {
                var start = $("#start-" + i).data("value") ? $("#start-" + i).data("value") : '7:30 am';
                var end = $("#end-" + i).data("value") ? $("#end-" + i).data("value") : '17:30';
                var lunch = $("#lunch-" + i).data("value") ? $("#lunch-" + i).data("value") : '0:30';
                if (i == 0) {
                    start = $("#start-" + i).data("value") ? $("#start-" + i).data("value") : '7:30 am';
                    end = $("#end-" + i).data("value") ? $("#end-" + i).data("value") : '18:00';
                    lunch = $("#lunch-" + i).data("value") ? $("#lunch-" + i).data("value") : '0:45';
                }
                $("#start-" + i).attr('disabled', false).val(start).trigger("change");
                $("#end-" + i).attr('disabled', false).val(end).trigger("change");
                $("#lunch-" + i).attr('disabled', false).val(lunch).trigger("change");
            }
        });
        $(".off-checkbox").trigger("change");
        <?php $js = ob_get_clean() ?>
        <?php $this->registerJs($js) ?>
    </script>
<?php } ?>
<style>
    <?php ob_start() ?>
    .datepicker.datepicker-inline table {
        width: 100%;
    }

    .datepicker.datepicker-inline .disabled.day {
        opacity: 0;
    }

    .form-control.krajee-datepicker {
        display: none;
    }

    input[class$='-timepicker'] {
        text-align: center;
        max-width: 85px;
        border: 1px solid #cacaca;
    }

    .ui-timepicker-standard a {
        font-size: 13px;
    }

    .ui-timepicker-standard, .ui-timepicker, .ui-timepicker-viewport {
        height: auto;
    }

    .off-checkbox {
        width: 100%;
        height: 20px;
        cursor: pointer;
    }

    <?php $css = ob_get_clean() ?>
    <?php $this->registerCss($css) ?>
</style>
