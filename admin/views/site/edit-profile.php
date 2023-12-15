<?php

use borales\extensions\phoneInput\PhoneInput;
use common\data\Countries;
use common\widgets\dashboard\PanelBox;
use common\widgets\inputs\ICheck;
use common\components\extensions\Select2;
use common\components\rbac\models\AssignmentForm;
use common\models\Account;
use common\models\Admin;
use common\models\Division;
use common\models\MainSector;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use diggindata\signaturepad\SignaturePadWidget;
use yii\bootstrap\Tabs;

/* @var $this View */

$this->title = 'Edit Profile';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="edit-profile">
    <div class="row">
        <div class="col-md-6">
            <?php
            $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <?php $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'edit',
                'color' => PanelBox::COLOR_BLUE
            ]);

            $panel->beginHeaderItem();
            ?>
            <?php
            $assignment_model = new AssignmentForm(Yii::$app->user->id);
            $user_division = @Division::findOne(@Account::getAdminDivisionID())->name;
            $user_main_sector = @MainSector::findOne(@Account::getAdminMainSectorId())->name;
            ?>

            <span>
                <?= strtoupper($assignment_model->roles[0]) ?>
            </span>
            <span>
                <?= !empty($user_division) ? '-' . $user_division : '' ?>
            </span>
            <span>
                <?= !empty($user_main_sector) ? '-' . $user_main_sector : '' ?>
            </span>

            <?php
            $panel->endHeaderItem();
            ?>

            <div class="row">
                <div class="col-sm-4 profile_picture_management">
                    <div class="image_uploader">
                        <?php $imagePath = $model->image_thumb_path;

                        if (file_exists($imagePath)) {
                            $imageUrl = $model->image_thumb_url;
                        } else {
                            $imageUrl = Yii::getAlias('@staticWeb') . '/images/user-default.jpg';
                        } ?>
                        <img id="image-preview" src="<?= $imageUrl ?>" class="img-circle" alt="<?= $model->name ?>" width="150" />
                        <div class="actions">
                            <label id="admin-image-upload-label" for="admin-image">
                                <?= FA::i(FA::_UPLOAD) ?>
                            </label>

                            <?php if (!empty($model->image)) : ?>
                                <?= Html::a(FA::i(FA::_TRASH), [
                                    'admin/delete-picture',
                                    'id' => $model->id,
                                    'coming_from'
                                    => 'site'
                                ], [
                                    'class' => 'btn text-color-danger',
                                    'data-confirm' => Yii::t("app", 'Are you sure you want to delete profile picture?'),
                                    'data-method' => 'post',
                                ]); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <input type="file" id="admin-image" name="Admin[image]" accept=".jpg, .jpeg">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'password_input')->textInput() ?>
                </div>
                <div class="col-sm-6">
                    <label>&nbsp;</label>
                    <?= $form->field($model, 'enable_notification')->widget(ICheck::className())->label('') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?=
                    $form->field($model, 'country')->widget(Select2::classname(), [
                        'data' => Countries::getCountriesList(),
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => Yii::t("app", 'Select a country ...')
                        ],
                    ])
                    ?>
                </div>
                <div class="col-md-6 phone_number_widget_div">
                    <?= $form->field($model, 'phone_number',)->widget(PhoneInput::className(), [
                        'jsOptions' => [
                            'preferredCountries' => ['ae'],
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?=
                    $form->field($model, 'timezone')->widget(Select2::classname(), [
                        'data' => Countries::getTimeZonesList(),
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => Yii::t("app", 'Select a time zone ...')
                        ],
                    ])
                    ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>

            <?php PanelBox::end() ?>
            <?php ActiveForm::end(); ?>

        </div>
        <div class="col-md-6">
            <?php
            $signatureForm = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'action' => ['site/save-signature'],
            ]);
            ?>
            <?php $panel = PanelBox::begin([
                'title' => "Your Signature",
                'icon' => 'edit',
                'color' => PanelBox::COLOR_BLUE
            ]); ?>

            <div class="col-sm-12">
                <?= $signatureForm->field($model, 'signature')->hiddenInput(['id' => 'admin-signature'])->label(false) ?>

                <?=
                SignaturePadWidget::widget([
                    'model' => $model,
                    'options' => ['style' => 'min-width:100%;min-height:200px;', 'value' => $model->signature_url],
                    'showSaveAsJpg' => false,
                    'showSaveAsPng' => false,
                ]);
                ?>
                <?php if (!empty($model->signature)) { ?>
                    <div style="position: relative; display: inline-block;">

                        <img id="signature-image" src="<?= $model->signature_url ?>" style="max-width: 300px; max-height: 300px; margin:20px 0;" alt="Signature Image">
                    </div>
                <?php } ?>
                <div class="form-group">
                    <?= Html::submitButton(FA::i(FA::_SAVE) . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
                    <?php if (!empty($model->signature)) { ?>

                        <?= Html::a(FA::i(FA::_TRASH) . ' ' . Yii::t('app', 'Delete'), [
                            'site/delete-signature',
                            'id' => $model->id,
                        ], [
                            'class' => 'btn btn-danger',
                            'data-confirm' => Yii::t("app", 'Are you sure you want to delete the signature?'),
                            'data-method' => 'post',
                            'id' => 'delete-signature',
                        ]); ?>
                    <?php } ?>
                </div>
            </div>

        </div>
        <?php PanelBox::end(); ?>
        <?php ActiveForm::end(); ?>

    </div>
</div>







<style>
    body {
        display: block;
        padding: 0
    }
</style>

<script>
    <?php ob_start(); ?>
    // Get the input field and the preview image element
    const input = document.getElementById('admin-image');
    const preview = document.getElementById('image-preview');
    const defaultImageUrl = '<?= $model->image_thumb_url ?>';
    canvas.addEventListener('click', function() {
        var imageDataURL = canvas.toDataURL('image/png');
        $("#admin-signature").val(imageDataURL);
        var img = new Image();
        img.src = imageDataURL;
    });
    $(".clear").on('click', function() {
        var imageDataURL = canvas.toDataURL('image/png');
        $("#admin-signature").val("");
        $("#signature-pad").val("");

    });

    // Add an event listener to the input field
    input.addEventListener('change', function() {
        // Check if a file is selected
        if (this.files && this.files[0]) {
            // Create a FileReader object
            const reader = new FileReader();

            // Set the image source to the data URL when it is loaded
            reader.addEventListener('load', function() {
                preview.src = reader.result;
            });

            // Read the selected file as a data URL
            reader.readAsDataURL(this.files[0]);
        } else {
            // If no file is selected, clear the preview image
            preview.src = defaultImageUrl;
        }
    });
    var phoneInput = document.getElementById("admin-phone_number");
    phoneInput.addEventListener("input", function(e) {
        var number = e.target.value;
        var numericValue = number.replace(/[^0-9+]/g, '');
        e.target.value = numericValue;
    });

    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js) ?>
</script>