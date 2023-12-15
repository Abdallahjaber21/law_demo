<?php

use common\components\extensions\DateRangeColumnInput;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $widget DateRangeColumnInput */

$id = "date-range-" . $widget->getId();
$fromDate = $widget->model->{$widget->attribute_from};
$toDate = $widget->model->{$widget->attribute_to};
?>

<div class="form-group">
    <?= Html::activeHiddenInput($widget->model, $widget->attribute_from) ?>
    <?= Html::activeHiddenInput($widget->model, $widget->attribute_to) ?>
    <div class="btn-group flex-center">
        <button type="button" class="btn" id="<?= $id ?>" style="background: transparent">
            <i class="fa fa-calendar"></i>&nbsp;
            <span>Select date range</span>
            <!--            <i class="fa fa-caret-down"></i>-->
        </button>
        <?php if ($widget->allowClear) { ?>
            <button id="clear-<?= $id ?>" type="button" class="btn" style="background: transparent; padding: 0">
                <i class="fa fa-close"></i>
            </button>
        <?php } ?>
    </div>
</div>


<script type="text/javascript">
    <?php ob_start() ?>
    //Date range as a button
    $('#<?= $id ?>').daterangepicker(
        {
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: <?= !empty($fromDate) ? "moment('{$fromDate}')" : date("Y-m-01") ?>,
            endDate: <?= !empty($toDate) ? "moment('{$toDate}')" : date("Y-m-d") ?>,
            maxDate: moment(),
            //showDropdowns: true,
            alwaysShowCalendars: true,
            showCustomRangeLabel: <?= $widget->showCustomRangeLabel ? 'true' : 'false' ?>,
            "maxSpan": {
                "days": <?= $widget->maxSpan ?>
            },
        },
        function (start, end) {
            $('#<?= $id ?> span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
        }
    );

    $('#<?= $id ?>').on('apply.daterangepicker', function (ev, picker) {
        $("#<?= Html::getInputId($widget->model, $widget->attribute_from) ?>").val(picker.startDate.format("YYYY-MM-DD"));
        $("#<?= Html::getInputId($widget->model, $widget->attribute_to) ?>").val(picker.endDate.format("YYYY-MM-DD"));
        $("#<?= Html::getInputId($widget->model, $widget->attribute_from) ?>").trigger("change");
        $("#<?= Html::getInputId($widget->model, $widget->attribute_to) ?>").trigger("change");
        <?php if ($widget->auto_submit) { ?>
        $(this).closest('form').submit();
        <?php } ?>
    });

    <?php if(!empty($fromDate) && !empty($toDate)){ ?>
    $('#<?= $id ?> span').html(
        moment("<?= $fromDate ?>").format('MMMM D, YYYY') +
        ' - ' + moment("<?= $toDate ?>").format('MMMM D, YYYY')
    );
    <?php } ?>
    $("#clear-<?= $id ?>").on("click", function () {
        $("#<?= Html::getInputId($widget->model, $widget->attribute_from) ?>").val("");
        $("#<?= Html::getInputId($widget->model, $widget->attribute_to) ?>").val("");
        $("#<?= Html::getInputId($widget->model, $widget->attribute_from) ?>").trigger("change");
        $("#<?= Html::getInputId($widget->model, $widget->attribute_to) ?>").trigger("change");
    });
    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>