<?php

use common\components\settings\Setting;
use yii\web\View;

/* @var $this View */

$this->params['title'] = Yii::t("app", "Products Expiry Notification");
$this->params['button_label'] = Yii::t("app", 'Call Us Now');
$this->params['button_link'] = "mailto:" . Yii::$app->params['supportEmail'];
$this->params['button_link'] = "tel:06432620";
//$this->params['image_link'] = Yii::getAlias("@staticWeb") . "/images/logo.png";
?>
<p>
<h3><?= $message ?></h3>
</p>
<?php if(false){?>
<table>
  <tr>
    <td>Email:</td>
    <td><a href="mailto:<?= Setting::getValue("contact_email") ?>"><?= Setting::getValue("contact_email") ?></a></td>
  </tr>
  <tr>
    <td>Phone:</td>
    <td><a href="tel:<?= Setting::getValue("contact_phone") ?>"><?= Setting::getValue("contact_phone") ?></a></td>
  </tr>
  <tr>
    <td>Address:</td>
    <td>
      <?= Setting::getValue("contact_address_1") ?><br/>
      <?= Setting::getValue("contact_address_2") ?>
    </td>
  </tr>
</table>
<?php } ?>