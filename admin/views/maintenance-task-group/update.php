<?php

use common\models\MaintenanceTaskGroup;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MaintenanceTaskGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model MaintenanceTaskGroup */

$this->title = Yii::t('app', 'Create Maintenance Task Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Maintenance Task Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="maintenance-task-group-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?=
            $this->render('_edit', [
                    'model'=>$model
            ])
            ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
