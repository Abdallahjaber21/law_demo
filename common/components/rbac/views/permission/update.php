<?php

use common\components\rbac\models\AuthItem;
use yii\web\View;

/* @var $this View */
/* @var $model AuthItem */

$this->title = Yii::t("app", "Update Permission: {name}", ['name' => $model->name]);
$this->params['breadcrumbs'][] = Yii::t("app", 'Roles & Permissions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-update">

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
