<?php

use common\components\notification\Notification;
use common\components\notification\NotificationMessages;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/* @var $this View */
/* @var $unseen integer */
/* @var $notifications Notification[] */
?>
<li class="dropdown notifications-menu">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
    <i class="fa fa-bell-o"></i>
    <span class="label label-warning">
      <?= $unseen ?>
    </span>
  </a>
  <ul class="dropdown-menu">
    <?php if (!empty($notifications)) { ?>
      <li class="header"><strong>
          <?=
            \Yii::t("app", "You have {count} unseen notifications", [
              'count' => $unseen
            ])
            ?>
        </strong></li>
      <li>
        <!-- inner menu: contains the actual data -->
        <ul class="menu">
          <?php foreach ($notifications as $key => $notification) { ?>
            <li class="<?= $notification->seen ? 'bg-gray-light' : '' ?>" data-id="<?= $notification->id ?>">
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
              <?= Html::a($icon .
                NotificationMessages::getMessage($notification->message, Json::decode($notification->params))
                . '<br/><small><small class="text-muted pull-left">'
                . Yii::$app->getFormatter()->asRelativeTime($notification->created_at)
                . '</small></small>', ['/notification/click', 'id' => $notification->id])
                ?>
            </li>
          <?php } ?>
        </ul>
      </li>
      <li class="text-center">
        <?= Html::a('View all', ['/notification/index'], ['class' => 'btn btn-default btn-flat btn-block']) ?>
      </li>
    <?php } else { ?>
      <li class="header">
        <?= \Yii::t("app", "You don't have any notification yet") ?>
      </li>
    <?php } ?>
  </ul>
</li>