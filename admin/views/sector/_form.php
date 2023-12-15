<?php

use common\components\extensions\Select2;
use common\models\MainSector;
use common\widgets\dashboard\PanelBox;
use yeesoft\multilingual\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\Country;
use common\models\State;
use common\models\Sector;
use common\models\City;
use kartik\depdrop\DepDrop;
use yii\web\JsExpression;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Sector */
/* @var $form ActiveForm */
?>

<div class="sector-form">

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin(); ?>
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                //'icon' => 'plus',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php $panel->beginHeaderItem() ?>
            <?= $form->languageSwitcher($model); ?>
            <?php $panel->endHeaderItem() ?>

            <div class="row">
                <div class="col-sm-6">
                    <?php /*$form->field($model, 'default_technician_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Technician::find()->where(['status'=>Technician::STATUS_ENABLED])->all(), 'id', 'name'),
                        'pluginOptions' => ['allowClear' => true],
                        'options' => [
                            'placeholder' => Yii::t('app', 'Default Technician...')
                        ],
                    ]) */ ?>
                    <?= $form->field($model, 'main_sector_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(MainSector::find()->where(['<>', 'status', MainSector::STATUS_DELETED])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        'options' => ['placeholder' => 'Select Main Sector'],

                    ]) ?>
                    <?= Html::hiddenInput('main_sector_id', $main_sector_id, ['class' => 'hiddenmain']) ?>

                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'country_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Country::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        'options' => ['placeholder' => 'Select a Country', 'id' => 'country-input']
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'state_id')->widget(DepDrop::className(), [
                        'data'           => ArrayHelper::map(State::find()->where(['id' => $model->state_id])->orderBy(['name' => SORT_ASC])->all(), 'id', function ($model) {
                            return "{$model->name}";
                        }),
                        'value' => $model->state_id,
                        'type'           => DepDrop::TYPE_SELECT2,
                        'options'        => [
                            'placeholder' => Yii::t("frontend", 'Select a State'),
                            'id' => 'state-input'
                        ],
                        'select2Options' => [
                            'theme'         => Select2::THEME_DEFAULT,
                            'pluginOptions' => [
                                "multiple"   => false,
                                'allowClear' => true,
                                'escapeMarkup'      => new JsExpression('function (markup) {return markup; }'),
                                'templateResult'    => new JsExpression('function(res) {return res.text; }'),
                                'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                            ]
                        ],
                        'pluginOptions'  => [
                            'depends'     => ["country-input"],
                            'initDepends' => [
                                "country-input"
                            ],
                            'initialize'  => true,
                            'url'         => Url::to(['/dependency/search-country-states']),
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'city_id')->widget(DepDrop::className(), [
                        'data'           => ArrayHelper::map(City::find()->where(['id' => $model->city_id])->orderBy(['name' => SORT_ASC])->all(), 'id', function ($model) {
                            return "{$model->name}";
                        }),
                        'value' => $model->city_id,
                        'type'           => DepDrop::TYPE_SELECT2,
                        'options'        => [
                            'placeholder' => Yii::t("frontend", 'Select a City'),
                        ],
                        'select2Options' => [
                            'theme'         => Select2::THEME_DEFAULT,
                            'pluginOptions' => [
                                "multiple"   => false,
                                'allowClear' => true,
                                'escapeMarkup'      => new JsExpression('function (markup) {return markup; }'),
                                'templateResult'    => new JsExpression('function(res) {return res.text; }'),
                                'templateSelection' => new JsExpression('function (res) { if(res.tag){$(".new-cooler").removeClass("hidden");}else{$(".new-cooler").addClass("hidden");} return res.text; }'),
                            ]
                        ],
                        'pluginOptions'  => [
                            'depends'     => ["state-input"],
                            'initDepends' => [
                                "country-input"
                            ],
                            'initialize'  => true,
                            'url'         => Url::to(['/dependency/search-state-cities']),
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    $statusList = $model->status_list;
                    if ($model->status != Sector::STATUS_DELETED) {
                        unset($statusList[Sector::STATUS_DELETED]);
                    }
                    $disabled = $model->status === Sector::STATUS_DELETED ? ['disabled' => true] : []; ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => $statusList,
                        'options' => $disabled,
                    ])
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
            </div>

            <?php PanelBox::end() ?> <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<script type="text/javascript">
    <?php ob_start() ?>
    var specificValue = $('.hiddenmain').val();
    if (specificValue != '') {

        $('.field-sector-main_sector_id select').val(specificValue).trigger('change');
    }
    <?php $js = ob_get_clean() ?> <?php $this->registerJs($js) ?>
</script>