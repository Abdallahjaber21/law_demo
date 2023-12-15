<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\components\rbac\models\AuthItem */

$this->title = Yii::t("app", "Update Role: {name}", ['name' => $model->name]);
$this->params['breadcrumbs'][] = Yii::t("app", 'Roles & Permissions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-update">

    <?= $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>