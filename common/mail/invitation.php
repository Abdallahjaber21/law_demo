<?php

use yii\web\View;

/* @var $this View */
/* @var $name string */
/* @var $code string */

$this->params['title'] = \Yii::t("app", 'Global Union E-Wallet');
$this->params['button_label'] = \Yii::t("app", 'Create Account');
$this->params['button_link'] = Yii::getAlias("@userWeb");
//$this->params['image_link'] = Yii::getAlias("@staticWeb") . "/images/logo.png";
?>
<?= \Yii::t("app", "Hello") ?>,<br/><br/>
<?= \Yii::t("app", "Your friend {name} has invited you to use Global Union E-Wallet.", ['name' => $name]) ?><br/><br/>
<?= \Yii::t("app", "Create your account now to connect to {name}.", ['name' => $name]) ?><br/><br/>

