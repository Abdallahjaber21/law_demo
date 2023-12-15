<?php
use common\models\Admin;
use common\models\Assignee;
use common\models\Log;
use common\models\RepairRequest;
use common\models\Technician;
use yii\helpers\ArrayHelper;


$actors = ArrayHelper::merge(ArrayHelper::map(Admin::find()->all(), 'id', 'name'), ArrayHelper::map(Technician::find()->all(), 'id', 'name'));
?>

<div class="timeline">
    <?php foreach ($items as $log) { ?>

        <?php
        $statuses = null;

        if ($log->type == Log::TYPE_REPAIR_REQUEST) {
            $statuses = (new RepairRequest())->status_list;
        } else if ($log->type == Log::TYPE_TECHNICIAN) {
            $statuses = ArrayHelper::merge((new Assignee())->status_list, (new Assignee)->acceptance_status_list);
        }
        ?>

        <div class="timeline-item">
            <div class="timeline-item-date">
                <?= Yii::$app->formatter->asDatetime($log->created_at) ?>
            </div>
            <div class="timeline-item-divider"></div>
            <div class="timeline-item-content">
                <div class="timeline-item-header">
                    <div class="author text-15 text-bold">
                        <?= $log->created_by == -1 ? "System" : $actors[$log->created_by] ?>
                    </div>
                    <div class="status">
                        <?= $statuses[$log->status] ?>
                    </div>
                </div>
                <div class="timeline-item-inner">
                    <div class="item-title">
                        <?= $log->title ?>
                    </div>
                    <div class="item-description">
                        <?= $log->description ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>