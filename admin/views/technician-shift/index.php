<?php


use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Shift;
use common\models\TechnicianShift;
use common\widgets\dashboard\DateRangeFilter;
use yii\helpers\ArrayHelper;
use common\widgets\inputs\assets\ICheckAsset;

$this->title = 'Manage Technician Shifts';
?>
<?php $form = ActiveForm::begin([
    'action' => ['save-shifts'], // Specify the URL of the actionSaveShifts action.
    'method' => 'post', // Use the POST method for form submission.
]); ?>
<?php $allshiftsid = Shift::find()->select(['id'])->all();
$allshiftsid = ArrayHelper::getColumn($allshiftsid, 'id');
?>
<div class="row top_section">
    <?php $currentYear = date('Y');
    $currentMonth = date('n'); ?>
    <?= DateRangeFilter::widget([
        'auto_submit' => false,
        'maxSpan' => 999999
    ]) ?>
    <?= Html::hiddenInput('fromday', Yii::$app->request->get('fromday'), ['id' => 'fromday', 'name' => 'fromday']); ?>
    <?= Html::hiddenInput('endday', Yii::$app->request->get('endday'), ['id' => 'endday', 'name' => 'endday']); ?>

    <?= Html::hiddenInput('selectedMonth', null, ['id' => 'selectedMonth', 'name' => 'selectedMonth']); ?>
    <?= Html::hiddenInput('selectedYear', null, ['id' => 'selectedYear', 'name' => 'selectedYear']); ?>
    <?= Html::hiddenInput('completedDate', null, ['id' => 'completedDate', 'name' => 'completedDate']); ?>
    <?= Html::hiddenInput('technicianShiftsData', '', ['id' => 'technicianShiftsData']); ?>

    <div class="col-md-4">
        <!-- <?= Html::Button('Filter', ['class' => 'btn btn-primary', 'id' => 'filterButton']) ?> -->
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'id' => 'saveButton']) ?>
    </div>
</div>
<?php //ActiveForm::end(); 
?>
<div class="row alltable">
    <div class="col-md-12 table-container">
        <table class="table technician col-md-3" id="technician-table" technician_id="<?= $technician_id ?>">

            <thead>
                <tr class="topheader">
                    <th></th>
                    <th></th>
                </tr>
                <tr class="sticky-header">
                    <th>Technician</th>
                    <th>Shifts</th>
            </thead>
            <tbody>
                <?php foreach ($technicians as $technician) : ?>
                <?php $shifts = $shiftData[$technician->id] ?? []; ?>
                <tr>
                    <td><?= Html::encode($technician->name) ?></td>
                    <td class=" shiftdate"><?php foreach ($allShiftTypes as $shiftType) : ?>
                        <label> <?= Html::encode($shiftType->name) ?> </label>

                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <table class="table shifts header ">
            <thead class="days">
                <tr class="topheader">
                    <th></th>
                    <?php foreach ($days as $day) : ?>
                    <?php $dateString = $selectedYear . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $dayName = date('D', strtotime($dateString)); ?>
                    <th><?= $dayName ?></th>
                    <?php endforeach; ?>
                </tr>
                <tr class="sticky-header">
                    <th class="sticky-header">All</th>
                    <?php foreach ($days as $day) : ?>
                    <th class="sticky-header"><?= $day ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class=" sticky-columns">
                <?php foreach ($technicians as $technician) : ?>
                <?php $shifts = $shiftData[$technician->id] ?? []; ?>

                <tr data-technician="<?= $technician->id ?>">
                    <td class="shiftdate">
                        <?php foreach ($allShiftTypes as $shiftType) : ?>
                        <?php $query = TechnicianShift::find()
                                    ->where(['technician_id' => $technician->id])
                                    ->andWhere(['shift_id' => $shiftType->id])
                                    ->andWhere(['YEAR(date)' => $selectedYear])
                                    ->andWhere(['MONTH(date)' => $selectedMonth]);
                                if ($query->count() == count($days)) {
                                    $allchecked = true;
                                } else {
                                    $allchecked = false;
                                }
                                ?>
                        <?= Html::checkbox("selectAll[{$technician->id}][$shiftType->id]", false, [
                                    'value' => $shiftType->name,
                                    'label' => '',
                                    'class' => "alldays shift-type-checkbox icheck",
                                    'data-technician' => $technician->id,
                                    'data-shift-type' => $shiftType->id,
                                    'checked' => $allchecked,

                                ]); ?>
                        <?php endforeach; ?>
                    </td>
                    <?php foreach ($days as $day) : ?>
                    <td class="shiftdate">
                        <?php foreach ($allShiftTypes as $shiftType) : ?>
                        <?php
                                    $shift_id = $shiftType->id;
                                    $selectedMonth = str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
                                    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                                    $isChecked = isset($shiftData[$technician->id][$selectedYear . '-' . $selectedMonth . '-' . $day][$shift_id]);
                                    $checkboxName = "shiftData[{$technician->id}][{$selectedYear}-{$selectedMonth}-{$day}][$shift_id]";
                                    ?>

                        <?= Html::checkbox("shiftData[{$technician->id}][{$day}][$shiftType->id]", $isChecked, [
                                        'value' => $shiftType->name,
                                        'label' => '',
                                        'class' => "shift icheck",
                                        'data-technician' => $technician->id,
                                        'data-shift-type' => $shiftType->id,
                                        'data-day' => $day,
                                        'checked' => $isChecked,
                                    ]); ?>

                        <?php endforeach; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php ActiveForm::end(); ?>
<style>
.content-header {
    display: none;
}

th {
    height: 37px;
}

#w1-success,
#w1-error {
    position: absolute;
    top: 0px;
    z-index: 9999;
    width: 100%;
    left: 0px
}

.alltable {
    margin-top: 100px;
    position: relative;
    width: 100%;
}

.ranges {
    display: none
}

.shiftdate label,
.technician label {
    display: flex;
    align-items: center;
    min-height: 22px;
    text-transform: capitalize;
    white-space: nowrap;

}

.top_section {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    position: fixed;
    z-index: 3;
    background: #fff;
    width: 100%;
    top: 60px;
    padding-top: 20px;
    padding-bottom: 20px
}

.drp-selected {
    display: none !important
}

.shifts {
    width: 100%;
    border-collapse: collapse;
}

body {
    position: relative;
    overflow: hidden;
}

label {
    position: relative;
}

.content {
    /* margin-top: 25%; */
    margin-top: 0px;
    padding-top: 0px
}

.row .col-md-12 {
    overflow-x: auto;
    overflow-y: auto;
    display: flex;
    padding-left: 0;
}

table {
    border-collapse: collapse;
}

tr {
    border: 1px solid rgba(0, 0, 0, 0.2)
}

input[type="radio"],
input[type="checkbox"] {
    margin-right: 5px
}

.tables {
    display: flex;
    overflow-x: auto;
    max-width: 100%;
}

.technician {
    flex-shrink: 0;
    position: sticky;
    left: 0;
    z-index: 2;
    background-color: #F4F4F4;
}

.table-container tbody {
    position: relative;
    overflow: auto;
    max-height: 80vh;
}


td.shiftdate {
    background: #F4F4F4
}

th {
    background: #FAFAFA
}

.technician td {
    padding-left: 20px !important
}

.technician th {
    padding-left: 15px !important
}

.table>thead>tr>th,
.table>tbody>tr>th,
.table>tfoot>tr>th,
.table>thead>tr>td,
.table>tbody>tr>td,
.table>tfoot>tr>td {
    border-top: 1px solid rgba(0, 0, 0, 0.2);
    border-right: 1px solid rgba(0, 0, 0, 0.2);
}

.col-md-3 {
    width: 25%;
}

.table>thead>tr>th {
    border-bottom: 1px solid rgba(0, 0, 0, 0.2) !important
}

.form-group {
    margin-bottom: 0;
}

.shifts {
    position: relative
}

.table-container {
    position: relative;
    overflow: hidden;
    max-height: 80vh;
}

tbody {
    overflow-y: scroll;
    max-height: 100%;
}

.technician {
    width: 200px;
    position: sticky;
    left: 0;
    background-color: #F4F4F4;
}

thead .topheader th {
    position: sticky;
    top: 0px;
    border: 1px solid rgba(0, 0, 0, 0.2);
    background-color: #FAFAFA;
    z-index: 1;
}

thead .sticky-header th {
    position: sticky;
    top: 36px;
    background-color: #FAFAFA;
    z-index: 1;
    border: 1px solid rgba(0, 0, 0, 0.2);

}

th {
    position: sticky;
    top: -10px;
    background-color: #FAFAFA;
    z-index: 1;
}

.daterangepicker td.active,
.daterangepicker td.active:hover {
    background-color: #0088cc;
    border-color: transparent;
    color: #fff;
    border-radius: 2px;
    background-image: linear-gradient(to bottom, #0088cc, #0044cc);
}

.icheckbox_square-blue {
    border-radius: 4px;
}
</style>



<?php ICheckAsset::register($this) ?>
<script>
<?php ob_start(); ?>
$('.icheck').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'icheckbox_square-blue',
    increaseArea: '20%'
});
<?php $js = ob_get_clean(); ?>
<?php $this->registerJs($js); ?>
</script>
<script>
<?php ob_start() ?>

var $header = $("thead th");
var $columns = $("tbody tr");
var $tableContainer = $(".table-container");

$tableContainer.on("scroll", function() {
    var scrollTop = $(this).scrollTop();
    if (scrollTop > 50) {
        $header.addClass("sticky");
    } else {
        $header.removeClass("sticky");
    }
    $columns.each(function() {
        var $this = $(this);
        var leftOffset = $this.offset().left;
        var rightOffset = leftOffset + $this.outerWidth();

        if (scrollTop > 50) {
            $this.addClass("sticky");
            $this.css("left", leftOffset + "px");
            $this.css("right", $(window).width() - rightOffset + "px");
        } else {
            $this.removeClass("sticky");
            $this.css("left", "");
            $this.css("right", "");
        }
    });
    if (scrollTop > 60) {
        $header.find("th").css("top", scrollTop + "px");

    } else {
        $header.find("th").css("top", "");


    }
});
$('#saveButton').click(function(e) {
    e.preventDefault();
    save();
});

function save() {
    var technicianShiftsData = {};
    const technician_id = $('#technician-table').attr('technician_id');
    const dateString = $('#_s').val();
    const date = new Date(dateString);
    const selectedyear = date.getFullYear();
    const selectedmonth = date.getMonth() + 1;
    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    var currentMonth = currentDate.getMonth() + 1;
    var currentDay = currentDate.getDate();

    $('.shifts tbody tr').each(function() {
        var technicianId = $(this).data('technician');
        var shiftsForTechnician = {};
        $(this).find('.shiftdate input:checked').each(function() {
            var parts = this.name.match(/shiftData\[(\d+)\]\[(\d+)\]\[(\w+)\]/);
            if (parts) {
                var technicianId = parts[1];
                var day = parts[2];
                var shiftType = parts[3];
                if (!shiftsForTechnician[day]) {
                    shiftsForTechnician[day] = [];
                }
                shiftsForTechnician[day].push(shiftType);
            }
        });
        technicianShiftsData[technicianId] = shiftsForTechnician;
        var technicianShiftsDataJSON = JSON.stringify(technicianShiftsData);

        $('#technicianShiftsData').val(technicianShiftsDataJSON);
    });
    var completedDate = new Date(selectedyear, selectedmonth - 1, 1);
    var day = completedDate.getDate();
    var formattedCompletedDate = selectedyear + '-' + selectedmonth + '-' + day;
    $('#completedDate').val(formattedCompletedDate);
    $('#technician_id').val(technician_id);
    $('#saveButton').submit();


};
$('.applyBtn ').click(function(e) {
    e.preventDefault();
    var dateRange = $('.drp-selected').text();
    const [startDateStr, endDateStr] = dateRange.split(' - ');
    const startDate = new Date(startDateStr);
    const endDate = new Date(endDateStr);
    const startDay = startDate.getDate();
    const startMonth = startDate.getMonth() + 1;
    const startYear = startDate.getFullYear();
    const endDay = endDate.getDate();
    const endMonth = endDate.getMonth() + 1;
    const endYear = endDate.getFullYear();
    const technician_id = "<?= Yii::$app->request->get('technician_id'); ?>";
    if (startMonth == endMonth && startYear == endYear) {
        var newURL = 'index?fromday=' + startDay + '&endday=' + endDay + '&month=' + startMonth +
            '&year=' + startYear + '&technician_id=' + technician_id;
        window.location.href = newURL;
    }

});
$(document).on('change', '.shift-type-checkbox', function() {
    var isChecked = $(this).prop('checked');
    var isChecked = $(this).prop('checked');
    var technicianId = $(this).data('technician');
    var shiftTypeId = $(this).data('shift-type');
    var technicianId = $(this).data('technician');
    var shiftTypeId = $(this).data('shift-type');
    var checkboxesToToggle = $('.shift[data-technician="' + technicianId + '"][data-shift-type="' +
        shiftTypeId + '"]');
    checkboxesToToggle.prop('checked', isChecked);
    $('.shift[data-technician="' + technicianId + '"][data-shift-type!="' + shiftTypeId + '"]').prop('checked',
        false);
    $('.alldays[data-technician="' + technicianId + '"]').not(this).prop('checked', false);
    $('.icheck').iCheck('update');


});


function getMonth(monthStr) {
    return new Date(monthStr + '-1-01').getMonth() + 1
}

$(document).on('change', '.shift', function() {
    var day = $(this).data('day');
    var technicianId = $(this).data('technician');
    $('.shift[data-technician="' + technicianId + '"][data-day="' + day + '"]').not(this).prop('checked',
        false);
    $('.alldays[data-technician="' + technicianId + '"]').prop('checked', false);
    $('.icheck').iCheck('update');

});
$('.shift').on('change', function() {
    var $row = $(this).closest('tr');
    var technicianId = $row.data('technician');
    var $allDaysCheckbox = $row.find('.alldays[data-technician="' + technicianId + '"]');
    var $shiftCheckboxes = $row.find('.shift[data-technician="' + technicianId + '"]');

    var allShiftsChecked = $shiftCheckboxes.length == $shiftCheckboxes.filter(':checked').length;

    $allDaysCheckbox.prop('checked', allShiftsChecked);
    $('.icheck').iCheck('update');
});

<?php $js = ob_get_clean() ?> <?php $this->registerJs($js) ?>
</script>