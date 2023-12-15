<?php

use common\widgets\dashboard\PanelBox;
use yii\widgets\Pjax;
use common\config\includes\P;
use rmrevin\yii\fontawesome\FA;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\models\Admin;
use common\widgets\inputs\assets\ICheckAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AdminSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Admin::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="admin-index">
    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <div class="button-container <?php echo (P::c(P::ADMINS_ADMIN_PAGE_NEW)) ? '' : 'buttonpostion'; ?>">
                <?php if (P::c(P::ADMINS_ADMIN_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary', 'style' => 'margin-right:10px']);
                }
                $panel->addButton(Yii::t('app', FA::i(FA::_EDIT) . ' Edit'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px', 'id' => 'show-buttons']);
                $panel->addButton(Yii::t('app', 'Cancel'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px;display:none', 'id' => 'cancel_btn']);
                ?>
                <?= $this->render('@app/views/common/_hidden', [
                    'hiddenAttributes' => $hiddenAttributes,
                    'model' => $searchModel,
                ]) ?>
            </div>

            <?php Pjax::begin(); ?>
            <?= $this->render('_grid', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'hiddenAttributes' => $hiddenAttributes,

            ]) ?>
            <?php Pjax::end(); ?>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div><?php ICheckAsset::register($this) ?>
<script>
    <?php ob_start(); ?>
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-green',
        increaseArea: '20%'
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>