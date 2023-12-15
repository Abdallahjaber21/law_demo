<?php

use common\components\settings\Setting;
use rmrevin\yii\fontawesome\FA;
use yii\web\View;

/* @var $this View */
/* @var $options array */
/* @var $tabs array */
?>
<div class="card wizard-card" data-color="green" id="wizardProfile">

  <div class="wizard-loader hidden">
    <?= FA::icon(FA::_CIRCLE_O_NOTCH)->spin() ?>
  </div>
  <form>
    <!--        
    You can switch ' data-color="orange" '  
    with one of the next bright colors: "blue", "green", "orange", "red"
    -->
    <div class="wizard-header">
      <h3>
        <?php if (!empty($options['title'])) { ?>
          <?= $options['title'] ?> <br>
        <?php } ?>
        <small>
          <?php if (!empty($options['subtitle'])) { ?>
            <?= $options['subtitle'] ?> <br>
          <?php } ?>
        </small>
      </h3>
    </div>

    <div class="wizard-navigation">
      <ul>
        <?php foreach ($tabs as $key => $tab) { ?>
          <li><a href="#<?= $tab['id'] ?>" data-toggle="tab"><?= $tab['title'] ?></a></li>
        <?php } ?>
      </ul>

    </div>

    <div class="tab-content">
      <?php foreach ($tabs as $key => $tab) { ?>
        <div class="tab-pane" id="<?= $tab['id'] ?>">
          <?= $tab['content'] ?>
        </div>
      <?php } ?>
    </div>
    <div class="wizard-footer height-wizard">
      <div class="pull-right">
        <input type='button' class='btn btn-next btn-fill btn-success btn-wd btn-sm' name='next' value='<?= Yii::t("app", "Next") ?>' />

        <button type='submit' class='btn btn-finish  btn-fill btn-success btn-wd btn-sm' name='skip' value="skip">
          <?=
          Yii::t("app", "Start your {days} days free trial", [
              'days' => Setting::getValue("trial_duration")
          ])
          ?> 
        </button>
        <?php if (false) { ?>
          <button type='submit' class='btn btn-finish btn-fill btn-success btn-wd btn-sm' name='finish' value='<?= Yii::t("app", "Finish") ?>' >
            <?= Yii::t("app", "Finish") ?>
          </button>
        <?php } ?>

      </div>

      <div class="pull-left">
        <?php if (!empty($options['exit-link'])) { ?>
          <a href="<?= $options['exit-link'] ?>" class='btn btn-exit btn-danger btn-fill btn-wd btn-sm' name='exit' value='<?= Yii::t("app", "Exit") ?>' >
            <?= Yii::t("app", "Exit") ?>
          </a>
        <?php } ?>
        <input type='button' class='btn btn-previous btn-default btn-wd btn-sm' name='previous' value='<?= Yii::t("app", "Previous") ?>' />
      </div>
      <div class="clearfix"></div>
    </div>
  </form>
</div>
