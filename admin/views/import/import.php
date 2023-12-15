<?php

use admin\models\BulkModelImportForm;
use common\components\extensions\Select2;
use common\models\Account;
use common\models\Admin;
use common\models\Division;
use common\widgets\dashboard\PanelBox;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model BulkModelImportForm */
/* @var $errors mixed */
/* @var $fields array */
/* @var $hint array */

//$this->title = Yii::t('app', 'Import');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-create">
    <div class="location-form">
        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(); ?>
                <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode($this->title),
                    'color' => PanelBox::COLOR_ORANGE
                ]);

                if (!empty($require_division) && $require_division) :
                    $panel->beginHeaderItem();

                    if (empty(Account::getAdminAccountTypeDivisionModel())) {
                        echo Select2::widget([
                            'name' => 'division_id',
                            // 'value' => '',
                            'data' => ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                            'options' => ['multiple' => false, 'placeholder' => 'Select Division ...', 'id' => 'division_select2'],
                            'pluginOptions' => [
                                'width' => '300px',
                            ],
                        ]);
                    } else {
                ?>

                        <h4><?= Yii::$app->user->identity->division->name ?></h4>
                        <input type="hidden" value="<?= Yii::$app->user->identity->division_id ?>" name="division_id">

                <?php
                    }
                    echo Html::a('Export', Url::to(['export/location-equipments', 'division' => '']), ['class' => 'btn btn-flat btn-success', 'id' => 'export-division-btn', 'disabled' => true, 'style' => 'margin-left:.5rem;']);
                    $panel->endHeaderItem();
                endif;
                ?>

                <?php if (!empty($errors)) { ?>
                    <?php foreach ($errors as $rowNumber => $error) { ?>
                        <ul>
                            <?php foreach ($error as $index => $item) { ?>
                                <ol>
                                    <strong>Row #<?= $rowNumber ?></strong>: <?= $index ?> - <?= $item ?>
                                </ol>
                            <?php } ?>
                            <!--                            <ol>&nbsp; -----</ol>-->
                        </ul>
                    <?php } ?>
                <?php } ?>

                <?php if (!empty($hint)) { ?>
                    <p class="text-danger"><?= $hint ?></p>
                <?php } ?>
                <?php if (!empty($fields)) { ?>
                    <?php foreach ($fields as  $field) { ?>
                        <?= $form->field($field['model'], $field['attribute'])->fileInput($field['options'])->label($field['label']) ?>
                    <?php } ?>
                <?php } ?>
                <?= $form->field($model, 'file')->fileInput(['accept' => 'xls,xlsx'])->label("Excel of Data to import") ?>
                <!-- <//?= $form->field($model, 'override_existing')->checkbox() ?> -->
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('admin', 'Upload'), ['class' => 'btn btn-success', 'id' => 'Upload_btn_file', 'disabled' => (!empty($require_division) &&  empty(Account::getAdminAccountTypeDivisionModel())) ? true : false]) ?>
                    <input type="hidden" id="template_download_path" value="<?= Yii::getAlias("@staticWeb/import/{replace}.xlsx") ?>">

                    <?php if(empty($require_division)): ?>
                        <?= Html::a('Download Template', (!empty($require_division) &&  empty(Account::getAdminAccountTypeDivisionModel())) ? "#" : Yii::getAlias("@staticWeb/import/{$template_path}.xlsx") , ['class' => 'btn btn-info' , 'id' => 'download-template-btn' , 'disabled' => !empty($require_division) ? true : false]) ?>
                   <?php  endif; ?>
                </div>
                <?php PanelBox::end() ?>
                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

<script>
    <?php ob_start(); ?>
    $("#division_select2").change(function() {
        let val = $(this).find(":selected").val();

        if (val) {
            $('#Upload_btn_file').prop("disabled", false);
            $('#export-division-btn').removeAttr("disabled");

            let old_href = $('#export-division-btn').attr('href').split("=")[0] + '=';

            old_href += val;

            $('#export-division-btn').attr('href', old_href);

            // $original_url_to_be_replaced = $("#template_download_path").val();

            // let divisions = <//?= json_encode((new Division())->name_list); ?>;
            // console.warn('<<< val >>>' ,  divisions[val.toLowerCase()]);

            // $('#download-template-btn').removeAttr("disabled");
            // $('#download-template-btn').attr('href', $original_url_to_be_replaced.replace('{replace}' , "locations-equipments-" + (divisions[val])?.toLowerCase()));
        } else {
            $('#Upload_btn_file').prop("disabled", true);
            $('#export-division-btn').attr("disabled", true);

            // $('#download-template-btn').attr("disabled", true);
            // $('#download-template-btn').attr('href', "#");

        }
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>