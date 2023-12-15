<?php

use common\components\rbac\models\Role;
use common\config\includes\P;
use common\models\Account;
use common\widgets\dashboard\PanelBox;
use common\widgets\inputs\assets\ICheckAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Role */

$rules = Yii::$app->authManager->getRules();
$rulesNames = array_keys($rules);
$rulesDatas = array_merge(['' => Yii::t('rbac', '(not use)')], array_combine($rulesNames, $rulesNames));

$authManager = Yii::$app->authManager;
$permissions = $authManager->getPermissionsByRole(Account::getAdminAccountTypeLabel());

$permissionsList = Json::decode(file_get_contents(Yii::getAlias("@common/config/includes/_advanced-admin-permissions-new.json")));

$keys = array_keys(ArrayHelper::getColumn($permissions, 'name'));


$allowed_categories = [];
$allowed_sections = [];

foreach ($keys as $key) {
    $parts = explode('_', $key);
    $category = $parts[0]; // Get the first part as the category
    $section = $parts[1]; // Get the first part as the category


    // Check if the category is not already in the $allowed_categories array
    if (!in_array($category, $allowed_categories)) {
        $allowed_categories[] = $category; // Add the category to the array
    }

    // Check if the category is not already in the $allowed_categories array
    if (!in_array($section, $allowed_sections)) {
        $allowed_sections[] = $section; // Add the category to the array
    }
}
?>

<div class="auth-item-form">

    <div class="row">
        <div class="col-md-12">
            <?php
            $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'lock',
                'color' => PanelBox::COLOR_BLUE
            ]);
            ?>
            <!-- <//?php
                    // if (!$model->isNewRecord && P::c(P::ADMINS_ROLE_PAGE_VIEW)) {
                    //     $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->name], [
                    //         'class'        => 'btn btn-danger',
                    //         'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    //         'data-method'  => 'post',
                    //     ]);
                    // }
                    ?> -->

            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'description')->textarea(['rows' => 1]) ?>
                </div>
                <!-- <div class="col-md-4">
                    <//?= $form->field($model, 'ruleName')->dropDownList($rulesDatas) ?>
                </div> -->
            </div>


            <?php foreach ($permissionsList as $categoryKey => $category) { ?>
                <?php if (in_array($categoryKey, $allowed_categories)) : ?>
                    <?php
                    $panelCategory = PanelBox::begin([
                        'title' => $category['label'],
                        'color' => PanelBox::COLOR_GREEN,
                        'body'  => false,
                    ]); ?>
                    <div class="table-responsive" id="<?= $categoryKey ?>">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <?php foreach ($category['groups'] as $groupKey => $group) { ?>
                                        <?php if (in_array($groupKey, $allowed_sections)) : ?>
                                            <?php if ($group['label'] != 'Section') : ?>
                                                <td style="padding: 0">
                                                    <table class="table table-bordered table-condensed table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th> <?= $group['label'] ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (isset($group['page'])) : ?>
                                                                <?php foreach ($group['page'] as $pageKey => $pageValue) { ?>
                                                                    <tr>
                                                                        <td style="padding-left: 1rem;">
                                                                            <?php if (is_array($pageValue)) { ?>
                                                                                <?php if (in_array($pageKey, ArrayHelper::getColumn($permissions, 'name'))) : ?>
                                                                                    <?php foreach ($pageValue['permissions'] as $permissionKey => $permission) { ?>
                                                                                        <input <?= in_array($permissionKey, $model->permissions) ? "checked" : "" ?> id="<?= $permissionKey ?>" type="checkbox" name="Role[permissions][]" disabled="disabled" value="<?= $permissionKey ?>" class="icheck">
                                                                                        <label for="<?= $permissionKey ?>" class="control-label"><?= $permission ?></label>
                                                                                    <?php } ?>
                                                                                <?php endif; ?>
                                                                            <?php } else { ?>
                                                                                <?= '<strong>' . $pageValue . '</strong>' ?>
                                                                            <?php } ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            <?php else : ?>
                                                <?php $panelCategory->beginHeaderItem(); ?>
                                                <td style="padding: 0">
                                                    <table class="table table-hover" style="margin: 0; border:none;">
                                                        <?php if (isset($group['page'])) : ?>
                                                            <?php foreach ($group['page'] as $pageKey => $pageValue) { ?>

                                                                <?php if (is_array($pageValue)) { ?>
                                                                    <?php foreach ($pageValue['permissions'] as $permissionKey => $permission) { ?>
                                                                        <?php if (in_array($permissionKey, ArrayHelper::getColumn($permissions, 'name'))) : ?>
                                                                            <tr>
                                                                                <td style="padding-left: 1rem; border:none;">
                                                                                    <input <?= in_array($permissionKey, $model->permissions) ? "checked" : "" ?> data-enable_section="<?= $categoryKey ?>" id=" <?= $permissionKey ?> icheck" type="checkbox" name="Role[permissions][]" value="<?= $permissionKey ?>" class="icheck enabled_checkbox">
                                                                                    <label for="<?= $permissionKey ?>" class="control-label"><?= $permission ?></label>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    <?php } ?>
                                                                <?php } ?>

                                                            <?php } ?>
                                                        <?php endif; ?>
                                                    </table>
                                                </td>
                                                <?php $panelCategory->endHeaderItem(); ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php $panelCategory->end(); ?>
                <?php endif; ?>

            <?php } ?>
            <?php if (false) { ?>
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
            <?php } ?>

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

    $('.enabled_checkbox').each(function(index, el) {
        let table_cat = $(el).data('enable_section');
        console.warn('<<< $(el) >>>', $(el).prop('checked'));
        if ($(el).prop('checked')) {
            // Checkbox is now checked
            $('#' + table_cat).find('.icheckbox_square-blue').removeClass('disabled');
            $('#' + table_cat).find('.icheckbox_square-blue').each(function(index, el) {
                // let input_id = $(this).find('input').prop('id');
                $(el).find('input').removeAttr('disabled');
                // $(this).find('input').val(input_id);
                // $(this).closest('.icheckbox_square-blue').prop('aria-checked', true);
            });
        }
    });


    $('.icheck').on('ifChanged', function(event) {
        let table_cat = $(this).data('enable_section');
        if ($(this).prop('checked')) {
            // Checkbox is now checked
            // $('#' + table_cat).find('.icheckbox_square-blue').addClass('checked');
            // $('#' + table_cat).find('.icheckbox_square-blue').each(function(index, el) {
            //     // let input_id = $(this).find('input').prop('id');
            //     $(this).find('input').prop('checked', true);
            //     // $(this).find('input').val(input_id);
            //     // $(this).closest('.icheckbox_square-blue').prop('aria-checked', true);
            // });
            $('#' + table_cat).find('.icheckbox_square-blue').removeClass('disabled');
            $('#' + table_cat).find('.icheckbox_square-blue').each(function(index, el) {
                // let input_id = $(this).find('input').prop('id');
                $(el).find('input').removeAttr('disabled');
                // $(this).find('input').val(input_id);
                // $(this).closest('.icheckbox_square-blue').prop('aria-checked', true);
            });
        } else {
            // Checkbox is now unchecked
            $('#' + table_cat).find('.icheckbox_square-blue').removeClass('checked');
            $('#' + table_cat).find('.icheckbox_square-blue').addClass('disabled');
            $('#' + table_cat).find('.icheckbox_square-blue').each(function(index, el) {
                $(this).find('input').prop('checked', false);
                $(this).find('input').attr('disabled', 'disabled');
                // $(this).closest('.icheckbox_square-blue').prop('aria-checked', false);
            });
        }
    });

    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js, View::POS_READY); ?>
</script>