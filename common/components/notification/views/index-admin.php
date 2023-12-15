<?php

use common\components\notification\Notification;
use common\components\notification\NotificationMessages;
use common\models\AdminNotifications;
use common\models\RepairRequest;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;
use yii\helpers\Json;
use rmrevin\yii\fontawesome\FA;

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
                $panel->addButton(Yii::t('app', Yii::t("app", 'Mark all as read')), ['read-all-admin'], ['class' => 'btn btn-warning']);
                $panel->addButton(Yii::t('app', Yii::t("app", 'Delete all')), ['delete-all-admin'], ['class' => 'btn btn-danger']);
            }
            ?>
            <ul class="nav nav-stacked">
                <?php if (!empty($notifications)) { ?>
                    <?php foreach ($notifications as $key => $notification) { ?>
                        <li class="<?= $notification->seen ? 'bg-gray-light' : '' ?>" data-id="<?= $notification->id ?>"
                            style="display: flex;align-items:center;justify-content:space-between;">
                            <?php
                            if (!empty($notification->type)) {

                                $icon = '<i class="fa fa-bell text-aqua"></i>';
                                if ($notification->type == AdminNotifications::TYPE_STATUS) {

                                    if ($notification->status == RepairRequest::STATUS_DRAFT || $notification->status == RepairRequest::STATUS_REQUEST_COMPLETION) {
                                        $icon = '<i class="fa fa-bell text-danger"></i>';
                                    } else if ($notification->type == AdminNotifications::TYPE_COORDINATES) {
                                        $icon = '<i class="fa fa-map text-aqua"></i>';
                                    }

                                }
                                // case Notification::TYPE_WARNING:
                                //   $icon = '<i class="fa fa-warning text-yellow"></i>';
                                //   break;
                                // default:
                                //   $icon = '<i class="fa fa-bell text-blue"></i>';
                                //   break;
                            }

                            ?>
                            <?=
                                Html::a($icon .
                                    AdminNotifications::getMessage($notification->type, $notification->request_id, $notification->technician_id, $notification->status)
                                    . '<br/><small><small class="text-muted pull-left">'
                                    . $notification->created_at
                                    . '</small></small>', $notification->getActionUrl(), ['style' => 'padding-block:2rem; flex:2;']);
                            ?>
                            <div class="right">
                                <?=
                                    Html::a(FA::i(FA::_TRASH), ['/notification/delete-admin', 'id' => $notification->id], ['style' => 'padding:2rem;font-size:20px;', 'class' => 'text-danger']);
                                ?>
                            </div>
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