<?php

// use common\components\notification\Notification;
use common\components\notification\NotificationMessages;
use common\models\AdminNotifications;
use common\models\RepairRequest;
use technician\modules\v1\controllers\RepairController;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use lavrentiev\widgets\toastr\Notification;
use yii\web\View;

/* @var $this View */
/* @var $unseen integer */
/* @var $notifications Notification[] */
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<input type="hidden" value="<?= $unseen ?>" id="unseen_hidden">


<!-- <li> -->
<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
  <i class="fa fa-bell-o"></i>
  <span class="label label-warning" id="unseen_count">
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
            if (!empty($notification->type)) {

              $icon = '<i class="fa fa-bell text-aqua"></i>';

              if ($notification->type == AdminNotifications::TYPE_STATUS) {
                if ($notification->status == RepairRequest::STATUS_DRAFT || $notification->status == RepairRequest::STATUS_REQUEST_COMPLETION) {
                  $icon = '<i class="fa fa-bell text-danger"></i>';
                }
              } else if ($notification->type == AdminNotifications::TYPE_COORDINATES) {
                $icon = '<i class="fa fa-map text-aqua"></i>';
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
                . '</small></small>', $notification->getActionUrl())
              ?>
          </li>
        <?php } ?>
      </ul>
    </li>
    <li class="text-center flex items-center align-center">
      <?= Html::a('Mark All As Read', '#', ['class' => 'btn btn-warning btn-flat btn-block text-white no-hover-bg', 'id' => 'mark_all_as_read_popup_btn']) ?>
      <?= Html::a('View all', ['/notification/index-admin'], ['class' => 'btn btn-success btn-flat btn-block no-margin text-white no-hover-bg']) ?>
    </li>
  <?php } else { ?>
    <li class="header">
      <?= \Yii::t("app", "You don't have any notification yet") ?>
    </li>
    <li class="text-center">
      <?= Html::a('View all', ['/notification/index-admin'], ['class' => 'btn btn-success btn-flat btn-block no-margin text-white no-hover-bg']) ?>
    </li>
  <?php } ?>
</ul>
<!-- </li> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
  <?php ob_start(); ?>

  let unseen = $("#unseen_hidden").val();

  function refreshNotifications() {
    $.ajax({
      url: `<?= Url::to(['/dependency/get-admin-notifications']) ?>?count=${unseen}`,
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response?.response) {
          $('#menu_container').empty();
          $('#menu_container').append(response?.response);

          if (response?.notify == true) {
            toastr.success('New Notification Received!!', 'New', {
              timeOut: 5000,
              "closeButton": true,
              "debug": false,
              "progressBar": true,
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
            });
          }

          unseen = response?.unseen;
        }
      }
    });
  }

  $x = setInterval(() => {
    refreshNotifications();

    // clearInterval($x);
  }, 5000);

  <?php $js = ob_get_clean(); ?>
  <?php $this->registerJs($js); ?>
</script>