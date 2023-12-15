<?php
/* @var $this View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use common\widgets\dashboard\PanelBox;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;

$this->title = $name;

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-error">

  <div class="row">
    <div class="error-panel col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
      <?php
      $panel = PanelBox::begin([
                  'color' => PanelBox::COLOR_RED,
                  'withBorder' => false,
      ]);
      ?>     
      <?php // print_r($exception) ?>

      <?php $statusCodeDigits = str_split($exception->statusCode) ?>
      <?php
      if (isset($statusCodeDigits[1])) {
        $statusCodeDigits[1] = "<span class='text-red'>{$statusCodeDigits[1]}</span>";
      }
      ?>
      <h1>
        <?= implode("", $statusCodeDigits) ?>
      </h1>

      <h2 class="text-center"><?= nl2br(Html::encode($message)) ?></h2>

      <p class="text-center">
        <?= Yii::t("app", "The above error occurred while the Web server was processing your request.") ?><br/>
        <?= Yii::t("app", "Please contact us if you think this is a server error. Thank you.") ?>
      </p>
      <p class="text-center">
        <?= Html::a(FA::icon("home") . " " . Yii::t("app", "Home Page"), ["/"], ['class' => 'btn btn-default']) ?>
      </p>

      <?php PanelBox::end() ?>            
    </div>
  </div>
</div>

<style type="text/css">
<?php ob_start() ?>
  .content-header{
    display: none;
  }
  .content-wrapper{
    position: relative;
  }
  .content{
    padding: 0;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
  }
<?php $css = ob_get_clean() ?>
<?php $this->registerCss($css) ?>
</style>