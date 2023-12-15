<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use rmrevin\yii\fontawesome\FA;
use common\widgets\inputs\assets\ICheckAsset;
use yii\helpers\Url;
?>
<?php $form = ActiveForm::begin([
    'id' => 'save-form',
    'action' => ['dependency/save-hidden-attributes'],
    'method' => 'post',
    'options' => ['style' => 'display:inline-block;']
]); ?>
<?= Html::hiddenInput('controller_id', Yii::$app->controller->id) ?>
<?= Html::hiddenInput('faded_columns', implode(',', $hiddenAttributes), ['id' => 'faded_columns']) ?><div
    class="form-group" style="display: none;">
    <?= Html::submitButton(Yii::t('app', FA::i(FA::_SAVE) . ' Save'), ['class' => 'btn btn-sm btn-success  disabled', 'id' => 'save_btn', 'style' => 'display:none;margin-left:6px', 'disabled' => 'true']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php if (!empty(array_filter($hiddenAttributes))) { ?>
<div class="btn-group" id="hidden-attributes-dropdown">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false">
        <?= FA::i(FA::_EYE_SLASH) ?> Hidden
    </button>
    <div class="dropdown-menu fade-in-dropdown">
        <?php $form = ActiveForm::begin([
                'id' => 'hidden-attributes-form',
                'action' => ['dependency/save-shown-attributes'],
                'method' => 'post',
            ]); ?>
        <div class="row no-before no-after">
            <div class="drop-item">
                <?= Html::checkbox(
                        'select-all-checkbox',
                        false,
                        ['class' => 'select-all-checkbox icheck', 'id' => 'allattributes']
                    ) ?>
                <label for="allattributes">All</label>
            </div>
            <?php foreach ($hiddenAttributes as $attribute) : ?>
            <?php if (!empty($attribute)) : ?>
            <div class="drop-item">
                <?= Html::checkbox(
                                'hidden-attributes[]',
                                false,
                                ['class' => 'hidden-attribute-checkbox icheck', 'value' => $attribute, 'id' => 'hidden-attribute-' . $attribute]
                            ) ?>
                <label for="hidden-attribute-<?= $attribute ?>">
                    <?= trim($model->getAttributeLabel($attribute)) ?>
                </label>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="dropdown-divider"></div>
        <?= Html::hiddenInput('controller_id', Yii::$app->controller->id);
            ?>
        <?= Html::submitButton('Save', ['class' => 'dropdown-item', 'id' => 'save-hidden-attributes', 'class' => ' btn btn-sm btn-success']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php } ?>
<script type="text/javascript">
<?php ob_start() ?>
$('.select-all-checkbox').change(function() {
    var checked = $(this).prop('checked');
    $('.hidden-attribute-checkbox').prop('checked', checked);
    $('.icheck').iCheck('update');

});
$('.hidden-attribute-checkbox').change(function() {
    if ($(this).hasClass('select-all-checkbox')) {
        $('.hidden-attribute-checkbox').prop('checked', $(this).prop('checked'));
    } else {
        var allChecked = $('.hidden-attribute-checkbox:checked').length === $('.hidden-attribute-checkbox')
            .length;
        $('.select-all-checkbox').prop('checked', allChecked);
    }
    $('.icheck').iCheck('update');
});
<?php $js = ob_get_clean() ?> <?php $this->registerJs($js) ?>
</script>