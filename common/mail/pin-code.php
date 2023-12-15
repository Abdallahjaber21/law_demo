<?php

use yii\web\View;

/* @var $this View */
/* @var $name string */
/* @var $code string */

$this->params['title'] = \Yii::t("app", 'New Pincode');
//$this->params['button_label'] = \Yii::t("app", 'Reset Password');
//$this->params['button_link'] = $link;
//$this->params['image_link'] = Yii::getAlias("@staticWeb") . "/images/logo.png";
?>
<?= \Yii::t("app", "Hello {name}", ['name' => $name]) ?>,<br/><br/>
<?= \Yii::t("app", "A new pincode has beed generated for You, kindly use it to create your own pincode") ?><br/><br/>
<p>
<h2><?= $code ?></h2>
</p>
