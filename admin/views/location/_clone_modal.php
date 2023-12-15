<?php

use common\models\LocationEquipments;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;

$form = ActiveForm::begin([
    'method' => 'post',
    'action' => 'clone'
]);

Modal::begin([
    'header'       =>  '<h2>' .
        Yii::t("app", !empty($model->code) ? $model->code . ' - ' . $model->name . ' - ' . $model->division->name : $model->name . ' - ' . $model->division->name) .
        '</h2>',
    'id' => 'modal-' . $model->id,
    'footer' => '<div class="buttons_rest">' . Html::button('Cancel', ['class' => 'btn btn-success', 'data-dismiss' => 'modal']) . ' ' . Html::submitButton('Clone', ['class' => 'btn btn-primary']) . '</div> ',
    'class' => 'location_equipment_modal',
    'options' => [
        'data-backdrop' => 'static',
    ],
]);

$new_model = new LocationEquipments();

?>

<div class="row">
    <input type="hidden" name="Location[id]" value="<?= $model->id ?>">
    <!-- <div class="col-sm-6"><//?= $form->field($model, 'name')->textInput() ?></div> -->
    <div class="col-sm-12"><?= $form->field($new_model, 'code')->textInput(['id' => 'clone_model_input', 'placeholder' => 'Enter a specific location code to clone it']) ?></div>
    <div class="col-sm-12"><?= $form->field($new_model, 'apply_all')->checkbox(['id' => 'apply_for_all_checkbox'])->label('Apply For All')->hint('If checked , the clone will be applied for all locations within the same division!', ['style' => 'color:red;']) ?></div>
    <!-- <div class="col-sm-4"><//?= $form->field($model, 'clone_qty')->textInput(['placeholder' => 'Quantity', 'type' => 'number']) ?></div> -->
</div>

<script>
    <?php ob_start(); ?>
    $('#apply_for_all_checkbox').change(function() {
        let checked = $(this).prop('checked');

        if (checked) {
            $("#clone_model_input").prop("disabled", 'disabled');
        } else {
            $("#clone_model_input").removeAttr("disabled");
        }
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>


<?php Modal::end(); ?>
<?php $form::end(); ?>