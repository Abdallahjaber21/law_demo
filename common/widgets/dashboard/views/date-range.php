<?php

use yii\web\View;

/* @var $this View */
/* @var $maxSpan integer */
/* @var $showCustomRangeLabel boolean */
?>

<input type="hidden" name="_s" id="_s" value="<?= $from ?>" />
<input type="hidden" name="_e" id="_e" value="<?= $to ?>" />
<div class="form-group">
    <div class="input-group">
        <button type="button" class="btn btn-md btn-flat pull-right" id="daterange-btn" style="background: transparent">
            <i class="fa fa-calendar"></i>&nbsp;
            <span></span>
            <i class="fa fa-caret-down"></i>
        </button>
    </div>
</div>


<script type="text/javascript">
<?php ob_start() ?>
var originalStartDate = moment("<?= $from ?>");
var originalEndDate = moment("<?= $to ?>");
$('#daterange-btn').daterangepicker({

        ranges: {
            'Today': [moment(), moment()],
            // 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            //'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            //'Next Month': [moment().subtract(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
            //'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },

        startDate: moment("<?= $from ?>"),
        endDate: moment("<?= $from ?>"),
        minDate: moment(),
        alwaysShowCalendars: true,
        showCustomRangeLabel: <?= $showCustomRangeLabel ? 'true' : 'false' ?>,
        "maxSpan": {
            "days": <?= $maxSpan ?>
        },
    },
    function(start, end) {
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
);
$('#daterange-btn').on('apply.daterangepicker', function(ev, picker) {
    if (picker.startDate.month() != picker.endDate.month()) {
        alert("Please select a date range within the same month.");
        $("#_s").val(picker.startDate.format("YYYY-MM-DD"));
        $("#_e").val(picker.endDate.format("YYYY-MM-DD"));
        picker.setStartDate(originalStartDate);
        picker.setEndDate(originalEndDate);
        $('#daterange-btn span').html(originalStartDate.format('MMMM D, YYYY') + ' - ' + originalEndDate.format(
            'MMMM D, YYYY'));
        return false;
    }
    $("#_s").val(picker.startDate.format("YYYY-MM-DD"));
    $("#_e").val(picker.endDate.format("YYYY-MM-DD"));
    <?php if ($auto_submit) { ?>
    $(this).closest('form').submit();
    <?php } ?>
});


$('#daterange-btn span').html(
    moment("<?= $from ?>").format('MMMM D, YYYY') +
    ' - ' + moment("<?= $to ?>").format('MMMM D, YYYY')
)

<?php $js = ob_get_clean() ?> <?php $this->registerJs($js) ?>
</script>