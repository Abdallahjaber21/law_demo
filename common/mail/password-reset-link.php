<?php

use yii\web\View;

/* @var $this View */

$this->params['title'] = \Yii::t("app", 'Reset Password');
$this->params['button_label'] = \Yii::t("app", 'Reset Password');
$this->params['button_link'] = $link;
//$this->params['image_link'] = Yii::getAlias("@staticWeb") . "/images/logo.png";
?>
<?= \Yii::t("app", "Hello") ?>,<br/><br/>
<?= \Yii::t("app", "Please click the `Reset Password` button or use the link below to reset your password.") ?><br/><br/>
<p>
  <a href="<?= $link ?>"><?= $link ?></a>
</p>

<p>
    Do not reply to this email!
</p>
