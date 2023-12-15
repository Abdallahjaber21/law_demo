<?php

use yii\web\View;

/* @var $this View */
/* @var $data array */

$this->params['title'] = $data['title'];
if (!empty($data['button_label'])) {
    $this->params['button_label'] = $data['button_label'];
}
if (!empty($data['button_link'])) {
    $this->params['button_link'] = $data['button_link'];
}
if (!empty($data['image_link'])) {
    $this->params['image_link'] = $data['image_link'];
}
?>
<?php if (!empty($data['name'])) { ?>
    <?= \Yii::t("app", "Hello {name}", ['name' => $data['name']]) ?>,
<?php } else { ?>
    <?= \Yii::t("app", "Hello") ?>,
<?php } ?>
<br/><br/>
<?= $data['message'] ?>

