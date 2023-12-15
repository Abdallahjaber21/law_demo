<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;


Modal::begin([
    'header' => '<h2>' .
        Yii::t("app", !empty($model->code) ? $model->code . ' - ' . $model->name : $model->name) .
        '</h2>',
    'id' => 'modal-' . $model->id,
    'footer' => '<div class="footer_row">' . Html::button('Auto Generate Codes', ['id' => 'generate_btn-' . $model->id, 'class' => 'btn btn-warning']) . ' <div class="buttons_rest">' . Html::button('Cancel', ['class' => 'btn btn-success', 'data-dismiss' => 'modal']) . ' ' . Html::submitButton('Save', ['class' => 'btn btn-primary']) . '</div> </div>',
    'class' => 'location_equipment_modal',
    'options' => [
        'data-backdrop' => 'static',
    ],
]);

?>

<input type="hidden" name="location_id" value="<?= $location_id ?>">

<div id="row-<?= $model->id ?>" class="row no-gap">
</div>
<?php Modal::end(); ?>

<script>
    <?php ob_start(); ?>
    $('#generate_btn-' + <?= $model->id ?>).click(function () {
        let columns = $('#row-' + <?= $model->id ?> + ' .field-location-equipment-codes input');

        $(columns).each(function (index, el) {

            let cat = '<?= htmlspecialchars($model->category->name, ENT_QUOTES, 'UTF-8') ?>'.substring(0, 3)
                .trim().toUpperCase();
            let type = '<?= htmlspecialchars($model->equipmentType->name, ENT_QUOTES, 'UTF-8') ?>'
                .substring(0, 3).trim().toUpperCase();
            let rand = '';
            for (let i = 0; i < 5; i++) {
                rand += Math.floor(Math.random() * 10); // Generate random digit (0-9)
            }

            let composed_autogenerated_string = cat + "-" + type + "-" + rand;
            $(el).val(composed_autogenerated_string);
        });
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>