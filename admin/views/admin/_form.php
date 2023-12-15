<?php

use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Division;
use common\models\MainSector;
use yii\helpers\ArrayHelper;
use common\models\Profession;
use common\models\Account;
use common\data\Countries;
use common\models\Admin;
use rmrevin\yii\fontawesome\FA;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\users\AbstractAccount;

/* @var $this yii\web\View */
/* @var $model common\models\Admin */
/* @var $form ActiveForm */
?>

<div class="admin-form">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <?php print_r($form->errorSummary($model)); ?>

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                //'icon' => 'plus',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <?php $panel->beginHeaderItem() ?>
            <?= $model->division ? 'Division: <b style="font-size:18px;">' . $model->division->name . '</b>' : '' ?>
            <?= $form->languageSwitcher($model); ?>
            <?php $panel->endHeaderItem() ?>

            <div class="row">
                <div class="col-sm-6 profile_picture_management">
                    <div class="image_uploader">
                        <?php $imagePath = $model->image_thumb_path;
                        if (file_exists($imagePath)) {
                            $imageUrl = $model->image_thumb_url;
                        } else {
                            $imageUrl = Yii::getAlias('@staticWeb') . '/images/user-default.jpg';
                        } ?>
                        <img id="image-preview" src="<?= $imageUrl ?>" class="img-circle" alt="<?= $model->name ?>"
                            width="150" />
                        <div class="actions">
                            <label id="admin-image-upload-label" for="admin-image">
                                <?= FA::i(FA::_UPLOAD) ?>
                            </label>
                            <?php if (!empty($model->image)): ?>
                                <?= Html::a(FA::i(FA::_TRASH), ['admin/delete-picture', 'id' => $model->id, 'coming_from' => ''], [
                                    'class' => 'btn text-color-danger', 'data-confirm' => Yii::t("app", 'Are you sure you want to delete profile picture?'),
                                    'data-method' => 'post',
                                ]); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <input type="file" id="admin-image" name="Admin[image]" accept=".jpg, .jpeg" />
                    <!-- <//?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?> -->
                </div>
                <div class="col-sm-6">
                    <div class="form-group required">
                        <?= $form->field($model, 'account_type')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(Account::getAdminHierarchy(false), 'id', 'label'),
                            'options' => [
                                'placeholder' => Yii::t("app", 'Select an Account'),
                                // 'value' => ArrayHelper::map(Account::getAdminHierarchy(false), 'id', 'label'),
                                'value' => !$model->isNewRecord ? [Account::getAdminAccountTypeID($model->id)] : null,
                                'id' => 'account_type_dropdown'
                            ],
                        ])
                            ?>
                    </div>
                </div>

                <?php if (empty(Account::getAdminAccountTypeDivisionModel())): ?>
                    <div class="col-sm-6">

                        <?= $form->field($model, 'division_id')->widget(DepDrop::className(), [
                            'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            'value' => $model->division_id,
                            'type' => DepDrop::TYPE_SELECT2,
                            'options' => [
                                'placeholder' => Yii::t("frontend", 'Select a Division'),
                                'id' => 'division-input'
                            ],
                            'select2Options' => [
                                'theme' => Select2::THEME_DEFAULT,
                                'pluginOptions' => [
                                    "multiple" => false,
                                    'allowClear' => false,
                                    'escapeMarkup' => new JsExpression('function (markup) {return markup; }'),
                                    'templateResult' => new JsExpression('function(res) {return res.text; }'),
                                    'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                                ]
                            ],

                            'pluginOptions' => [
                                'depends' => ["account_type_dropdown"],
                                'initDepends' => [
                                    "account_type_dropdown"
                                ],
                                'initialize' => true,
                                'url' => Url::to(['/dependency/search-division-type']),
                            ]
                        ]) ?>
                    </div>
                <?php else: ?>
                    <div class="col-sm-6" style="display: none;">
                        <?= $form->field($model, 'division_id')->textInput(['hidden' => true, 'readOnly' => 'true', 'value' => Account::getAdminAccountTypeDivisionModel()->id]); ?>
                    </div>
                <?php endif; ?>

                <div class="col-sm-6">
                    <?php if (empty(Account::getAdminAccountTypeDivisionModel())): ?>
                        <?= $form->field($model, 'main_sector_id')->widget(DepDrop::className(), [
                            'data' => ArrayHelper::map(MainSector::find()->where(['id' => $model->main_sector_id])->andWhere(['<>', 'status', MainSector::STATUS_DELETED])->orderBy(['name' => SORT_ASC])->all(), 'id', function ($model) {
                                return "{$model->name}";
                            }),
                            'value' => $model->main_sector_id,
                            'type' => DepDrop::TYPE_SELECT2,
                            'options' => [
                                'placeholder' => Yii::t("frontend", 'Select a Main Sector'),
                            ],
                            'select2Options' => [
                                'theme' => Select2::THEME_DEFAULT,
                                'pluginOptions' => [
                                    "multiple" => false,
                                    'allowClear' => true,
                                    'escapeMarkup' => new JsExpression('function (markup) {return markup; }'),
                                    'templateResult' => new JsExpression('function(res) {return res.text; }'),
                                    'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                                ]
                            ],
                            'pluginOptions' => [
                                'depends' => ["division-input"],
                                'initDepends' => [
                                    "division-input"
                                ],
                                'initialize' => true,
                                'url' => Url::to(['/dependency/search-main-division']),
                            ]
                        ]) ?>
                    <?php else: ?>
                        <!-- <//?= $form->field($model, 'main_sector_id')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(MainSector::find()->where(['division_id' => Yii::$app->user->identity->division_id])->all(), 'id', 'name'),
                            'options'        => [
                                'placeholder' => Yii::t("frontend", 'Select a Sector'),
                            ],
                        ]); ?> -->
                        <div class="col-sm-6" style="display: none;">
                            <?= $form->field($model, 'main_sector_id')->textInput(['hidden' => true, 'readOnly' => 'true', 'value' => Account::getAdminMainSectorId()]); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    $statusList = $model->status_list;
                    if ($model->status != AbstractAccount::STATUS_DELETED) {
                        unset($statusList[AbstractAccount::STATUS_DELETED]);
                    }
                    $disabled = $model->status === Admin::STATUS_DELETED ? ['disabled' => true] : []; ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $statusList,
                        'options' => $disabled,
                    ])
                        ?>
                </div>


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
                    <?= $form->field($model, 'phone_number', )->widget(PhoneInput::className(), [
                        'jsOptions' => [
                            'preferredCountries' => ['ae'],
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'password_input')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?php if (empty($model->timezone)) {
                        $model->timezone = "Asia/Dubai";
                    } ?>
                    <?=
                        $form->field($model, 'timezone')->widget(Select2::classname(), [
                            'data' => Countries::getTimeZonesList(),
                            'theme' => Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => Yii::t("app", 'Select a Time Zone ...')
                            ],
                        ])
                        ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'badge_number')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                </div>

            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat', 'id' => 'save_btn_no_account_type']) ?>
            </div>

            <?php PanelBox::end() ?>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<script>
    <?php ob_start(); ?>
    // Get the input field and the preview image element
    const input = document.getElementById('admin-image');
    const preview = document.getElementById('image-preview');
    const defaultImageUrl = '<?= $model->image_thumb_url ?>';

    // Add an event listener to the input field
    input.addEventListener('change', function () {
        // Check if a file is selected
        if (this.files && this.files[0]) {
            // Create a FileReader object
            const reader = new FileReader();

            // Set the image source to the data URL when it is loaded
            reader.addEventListener('load', function () {
                preview.src = reader.result;
            });

            // Read the selected file as a data URL
            reader.readAsDataURL(this.files[0]);
        } else {
            // If no file is selected, clear the preview image
            preview.src = defaultImageUrl;
        }
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js) ?>
</script>

<script>
    <?php ob_start(); ?>
    let dropd_val = $('#account_type_dropdown').find(':selected').val();
    let input_val = $('#account_type_input').val();

    if (!dropd_val && !input_val) {
        $('#save_btn_no_account_type').prop('disabled', true);
    }

    $('#account_type_dropdown').change(function () {
        let value = $(this).find(':selected').val();
        if (value) {
            $('#save_btn_no_account_type').removeAttr('disabled');
        }
    });
    var phoneInput = document.getElementById("admin-phone_number");
    phoneInput.addEventListener("input", function (e) {
        var number = e.target.value;
        var numericValue = number.replace(/[^0-9+]/g, '');
        e.target.value = numericValue;
    });


    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>