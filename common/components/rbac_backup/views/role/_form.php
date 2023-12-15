<?php

use common\widgets\dashboard\PanelBox;
use common\widgets\inputs\assets\ICheckAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$rules = Yii::$app->authManager->getRules();
$rulesNames = array_keys($rules);
$rulesDatas = array_merge(['' => Yii::t('rbac', '(not use)')], array_combine($rulesNames, $rulesNames));

$authManager = Yii::$app->authManager;
$permissions = $authManager->getPermissions();
?>

<div class="auth-item-form">

    <div class="row">
        <div class="col-md-12">
            <?php
            $panel = PanelBox::begin([
                        'title' => $this->title,
                        'icon' => 'lock',
                        'color' => PanelBox::COLOR_BLUE
            ]);
            ?>
            <?php
            if (!$model->isNewRecord && Yii::$app->getUser()->can("rbac-delete-roles")) {
                $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->name], [
                    'class' => 'btn btn-danger',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                ]);
            }
            ?>

            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'description')->textarea(['rows' => 1]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'ruleName')->dropDownList($rulesDatas) ?>
                </div>
            </div>



            <div class="form-group field-role-permissions">
                <label class="control-label" for="role-permissions">Permissions</label>
                <input type="hidden" name="Role[permissions]" value="">
                <div id="role-permissions">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <td style="width:1px"></td>
                                <td style=""><b>Name</b></td>
                                <td><b>Description</b></td>
                                <td style="width:1px"></td>
                                <td style=""><b>Name</b></td>
                                <td><b>Description</b></td>
                                <td style="width:1px"></td>
                                <td style=""><b>Name</b></td>
                                <td><b>Description</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $perms = array_values($permissions) ?>
                            <?php for ($index = 0; $index < count($perms); $index += 3) { ?>
                                <tr>
                                    <?php for ($x = 0; $x < 3; $x++) { ?>
                                        <?php if (!empty($perms[$index + $x])) { ?>
                                            <?php $permission = $perms[$index + $x] ?>
                                            <td>
                                                <input <?= in_array($permission->name, $model->permissions) ? "checked" : "" ?> type="checkbox" name="Role[permissions][]" value="<?= $permission->name ?>" class="icheck">
                                            </td>
                                            <td><?= $permission->name ?></td>    
                                            <td><?= $permission->description ?></td>
                                        <?php } ?>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>           
                </div>
                <div class="help-block"></div>        
            </div>

            <?php if (!Yii::$app->request->isAjax) { ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('rbac', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-primary btn-flat' : 'btn btn-primary btn-flat']) ?>
                </div>
            <?php } ?>

            <?php ActiveForm::end(); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>

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