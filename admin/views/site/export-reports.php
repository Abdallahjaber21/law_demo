<?php


/* @var $this View */

use common\widgets\dashboard\DateRangeFilter;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;
use yii\web\View;

/* @var $from array|false|mixed|string */
/* @var $to array|false|mixed|string */

$this->title = "Export Reports";
$range = range(0, 23);
?>

<?php
$panel = PanelBox::begin([
    'title' => $this->title,
    'icon'  => 'dashboard',
    //'body' => false,
    'color' => PanelBox::COLOR_ORANGE
]);
?>

<?= Html::beginForm() ?>
<?= DateRangeFilter::widget(['auto_submit' => false]) ?>
    <div class="row">
        <div class="col-md-2">
            <label class="control-label">From Hour</label>
            <?= Html::dropDownList("from_hour", null, $range, [
                'class' => 'form-control'
            ]) ?>
        </div>
    </div>
<br/>
<?= Html::submitButton('Export', ['class' => 'btn btn-primary']) ?>
<?= Html::endForm() ?>
<?php PanelBox::end() ?>