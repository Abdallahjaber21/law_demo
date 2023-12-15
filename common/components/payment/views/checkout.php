<?php

use common\components\settings\Setting;
use yii\helpers\Inflector;
use yii\web\View;

/* @var $this View */
/* @var $sessionId string  */
?>
  <?php
$this->registerJsFile(Yii::$app->payment->checkoutUrl, [
//    'data-error' => 'errorCallback',
//    'data-cancel' => 'cancelCallback',
    'position' => View::POS_HEAD
]);
?>

<script type="text/javascript">
<?php ob_start() ?>
  function errorCallback(error) {
    console.log(JSON.stringify(error));
  }
  function cancelCallback() {
    console.log('Payment cancelled');
  }

  Checkout.configure({
    merchant: "<?= Yii::$app->payment->merchantId ?>",
    userId: "<?= Yii::$app->getUser()->getId() ?>",
    session: {
      id: "<?= $sessionId ?>"
    },
    interaction: {
      merchant: {
        name: '<?= Yii::$app->params["project-name"] ?>',
        logo: 'https://static.search2go.app/images/logo-sm.png',
        email: '<?= Setting::getValue("contact_email") ?>',
        phone: '<?= Setting::getValue("contact_phone") ?>',
        address: {
          line1: '<?= Setting::getValue("contact_address_1") ?>',
          line2: '<?= Setting::getValue("contact_address_2") ?>'
        }
      },
      //theme :"",
      displayControl: {
        billingAddress: "OPTIONAL",
        customerEmail: "OPTIONAL",
        paymentConfirmation: "SHOW",
        paymentTerms: "SHOW_IF_SUPPORTED",
      },
      locale: "<?php Inflector::camel2id(Inflector::id2camel(Yii::$app->language), "_") ?>",
    }
  });
  Checkout.showPaymentPage();
<?php $js = ob_get_clean() ?>
<?php $this->registerJs($js, View::POS_HEAD) ?>
</script>

