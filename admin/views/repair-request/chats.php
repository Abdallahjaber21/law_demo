<?php

use common\widgets\dashboard\PanelBox;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "#" . $model->id . " Messages";

$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['repair-request/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="chats-index row">
    <div class="col-sm-12 messages">
        <?php
        $panel = PanelBox::begin([
            'title' => Html::encode("Messages"),
            'icon' => 'wechat',
            'color' => PanelBox::COLOR_ORANGE,
            // 'panelClass' => ($model->status < RepairRequest::STATUS_CHECKED_IN && $model->urgent_status)  ? 'box-urgent' : ''
        ]);

        if (count($chats) > 0) { ?>
        <div class="messages">
            <?php foreach ($chats as $message) {
                    $image = @$message->gallery->images;
                ?>
            <div
                class="message <?= Yii::$app->user->id == $message->assignee_id ? "message-sent" : "message-received" ?>">
                <div class="message-avatar"
                    style="opacity: 1; background-image:url('<?= $message->assignee->user->image_url ?>');">
                </div>
                <div class="message-content">
                    <!-- <div class="message-name">John Doe</div> -->
                    <div class="message-header">
                        <?= Yii::$app->user->id != $message->assignee_id ? $message->assignee->user->name : "" ?>
                    </div>
                    <div class="message-bubble">
                        <?php if (!empty($message->gallery_id)) { ?>
                        <?php foreach ($image as $im) { ?>
                        <div class="message-image">
                            <img style="max-width:200px;object-fit: cover;" src="<?= $im->image_url ?>" alt="#">
                        </div>
                        <?php } ?>
                        <?php } ?>

                        <?php if (!empty($message->audio)) { ?>
                        <audio controls preload="none">
                            <source src="<?= $message->getAudioUrl($message->audio) ?>" type="audio/mpeg">
                        </audio>
                        <?php } ?>
                        <!-- <div class="message-text-header">Text header</div> -->
                        <div class="message-text">
                            <?= $message->message ?>
                        </div>
                        <!-- <div class="message-text-footer">Text footer</div> -->
                    </div>
                    <div class="message-footer" style="margin-bottom: unset !important;">
                        <?= $message->created_at ?>
                    </div>
                </div>
            </div>
            <?php
                } ?>
        </div>
        <?php } ?>

        <!-- <//?php
        $panel->beginFooter();

        echo Html::beginForm(
            Url::to(['repair-request/send-message', 'id' => $model->id]),
            'post',
            [
                'class' => 'flex align-center'
            ]
        );
        ?>
        <label for="upload_img" style="font-size: 25px; margin:.5rem 2rem .5rem 1rem; cursor: pointer;">
            <//?= FA::i(FA::_FILE_PHOTO_O) ?>
        </label> -->
        <!-- <input id="upload_img" type="file" accept="image/png, image/jpeg" name="image" style="display: none;">
        <input type="text" class="form-control" placeholder="Your Message" name="message">
        <button type="submit" class="btn btn-flat btn-warning">Send</button> -->
        <?php

        // Html::endForm();
        // $panel->endFooter();
        $panel::end();
        ?>
    </div>
</div>