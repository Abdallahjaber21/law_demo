<?php

use yii\web\View;

/* @var $this View */
/* @var $name string */
/* @var $code string */

$this->params['title'] = \Yii::t("app", 'Identity Verification');
//$this->params['button_label'] = \Yii::t("app", 'Reset Password');
//$this->params['button_link'] = $link;
//$this->params['image_link'] = Yii::getAlias("@staticWeb") . "/images/logo.png";
?>
<?= \Yii::t("app", "Hello {name}", ['name' => $name]) ?>,<br/><br/>
<?= \Yii::t("app", "Please use the code below to verify your idendity.") ?><br/><br/>
<p>
<h2><?= $code ?></h2>
</p>
