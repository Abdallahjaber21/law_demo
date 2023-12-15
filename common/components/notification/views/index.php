<?php

use common\components\notification\Notification;
use common\components\notification\NotificationMessages;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/* @var $this View */
/* @var $notifications Notification[] */

$this->title = Yii::t("app", "Notifications");

$this->params['breadcrumbs'][] = $this->title;
?>


<div class="notification-index">

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php
            $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'bell',
                'color' => PanelBox::COLOR_BLUE,
                'body' => empty($notifications)
            ]);
            ?>
            <?php
            if (!empty($notifications)) {
                $panel->addButton(Yii::t('app', Yii::t("app", 'Mark all as read')), ['read-all'], ['class' => 'btn btn-warning']);
                $panel->addButton(Yii::t('app', Yii::t("app", 'Delete all')), ['delete-all'], ['class' => 'btn btn-danger']);
            }
            ?>
            <ul class="nav nav-stacked">
                <?php if (!empty($notifications)) { ?>
                    <?php foreach ($notifications as $key => $notification) { ?>
                        <li class="<?= $notification->seen ? 'bg-gray-light' : '' ?>"
                            data-id="<?= $notification->id ?>">
                            <?php
                            switch ($notification->type) {
                                case Notification::TYPE_NOTIFICATION:
                                    $icon = '<i class="fa fa-bell text-aqua"></i>';
                                    break;
                                case Notification::TYPE_WARNING:
                                    $icon = '<i class="fa fa-warning text-yellow"></i>';
                                    break;
                                default:
                                    $icon = '<i class="fa fa-bell text-blue"></i>';
                                    break;
                            }
                            ?>
                            <?=
                            Html::a('<small class="text-muted pull-right" style="margin-left: 5px;">'
                                . Yii::$app->getFormatter()->asDatetime($notification->created_at)
                                . '</small>' . $icon . " &nbsp;" .
                                NotificationMessages::getMessage($notification->message, Json::decode($notification->params))
                                , ['/notification/click', 'id' => $notification->id])
                            ?>
                        </li>
                    <?php } ?>
                <?php } else { ?>
                    <li class="">
                        <?= Yii::t("app", "You don't have any notification") ?>
                    </li>
                <?php } ?>
            </ul>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
