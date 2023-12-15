<?php

use common\models\Account;
use common\widgets\dashboard\PanelBox;
use common\widgets\inputs\assets\ICheckAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\ActiveForm;

/** Get all roles */
$authManager = Yii::$app->authManager;

$this->title = Yii::t('app', 'Roles Assignments');
$this->params['breadcrumbs'][] = Yii::t("app", 'Roles & Permissions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles Assignments'), 'url' => ['index']];

// $crud_opts = Account::getAdminOptionsCrud();
$crud_opts = ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name');
?>


<div class="user-assignment-form">

    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <?php
            $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'lock',
                'color' => PanelBox::COLOR_BLUE
            ]);
            ?>
            <?php $form = ActiveForm::begin(); ?>
            <?= Html::activeHiddenInput($formModel, 'userId') ?>
            <label class="control-label"><?= $formModel->attributeLabels()['roles'] ?></label>
            <input type="hidden" name="AssignmentForm[roles]" value="">
            <table class="table table-striped table-bordered detail-view">
                <thead>
                    <tr>
                        <th style="width:1px"></th>
                        <th style="width:150px">Name</th>
                        <th>Description</th>
                    </tr>
                <tbody>
                    <?php foreach ($authManager->getRoles() as $key => $role) : ?>
                        <tr>
                            <?php
                            if (!in_array($role->name, $crud_opts)) {
                                continue;
                            }
                            ?>
                            <?php
                            $checked = true;
                            if ($formModel->roles == null || !is_array($formModel->roles) || count($formModel->roles) == 0) {
                                $checked = false;
                            } else if (!in_array($role->name, $formModel->roles)) {
                                $checked = false;
                            }
                            ?>
                            <td><input <?= $checked ? "checked" : "" ?> type="radio" name="AssignmentForm[roles][]" value="<?= $role->name ?>" class="icheck"></td>
                            <td><?= $role->name ?></td>
                            <td><?= $role->description ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (!Yii::$app->request->isAjax) { ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('rbac', 'Save'), ['class' => 'btn btn-primary btn-flat']) ?>
                </div>
            <?php } ?>
            <?php ActiveForm::end(); ?>

            <?php PanelBox::end() ?>
        </div>
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