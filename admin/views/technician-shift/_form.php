<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Technicians';

$months =
    [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"

    ];

$form = ActiveForm::begin();
?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'date')->dropDownList($months, ['prompt' => 'Select a month']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<div class="row">
    <div class="col-md-12">
        <h2>Technicians</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Day</th>
                    <?php foreach ($model as $mod) : ?>
                        <?php if (!empty($mod->name)) { ?> <th><?= Html::encode($mod->name) ?></th><?php } ?>
                    <?php endforeach; ?>
                </tr>
            </thead>

        </table>
    </div>
    <?php $list = array();
    $month = 8;
    $year = 2023;

    for ($d = 1; $d <= 31; $d++) {
        $time = mktime(12, 0, 0, $month, $d, $year);
        if (date('m', $time) == $month)
            $list[] = date('Y-m-d-D', $time);
    }
    echo "<pre>";
    print_r($list);
    echo "</pre>"; ?>
</div>