<?php

use yii\helpers\Html;
use yii\mail\MessageInterface;
use yii\web\View;

/* @var $this View */
/* @var $message MessageInterface *///the message being composed 
/* @var $content string */// main view render result 
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-Equiv="Cache-Control" Content="cache"/>
    <meta http-Equiv="Pragma" Content="cache"/>
    <meta http-Equiv="Expires" Content="1000"/>
    <?= $this->renderFile(__DIR__ . DIRECTORY_SEPARATOR . 'email-style.php') ?>
    <?php $this->head() ?>
</head>
<body offset="0" class="body"
      style="padding:0; margin:0; display:block; background:#eeebeb; -webkit-text-size-adjust:none" bgcolor="#eeebeb">
<?php $this->beginBody() ?>
<table align="center" cellpadding="0" cellspacing="0" width="100%" height="100%">
    <tr>
        <td align="center" valign="top" style="background:#fafafa" width="100%">
            <center>
                <table style="margin:0 auto;" cellspacing="0" height="60" cellpadding="0" width="100%">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <a href="#"><img height="90" src="<?= Yii::getAlias("@staticWeb") . "/images/logo.png" ?>"
                                             alt="<?= Yii::$app->params['project-name'] ?>"/></a>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <table cellspacing="0" cellpadding="0" width="600" class="w320"
                       style="border-radius: 4px;overflow: hidden;">
                    <tr>
                        <td align="center" valign="top">
                            <table cellspacing="0" cellpadding="0" class="force-full-width">
                                <tr>
                                    <td class="bg bg1" style="background-color:#fff;">
                                        <table cellspacing="0" cellpadding="0" class="force-full-width">

                                            <?php if (!empty($this->params['title'])) { ?>
                                                <tr>
                                                    <td style="font-size:26px; font-weight: 600; color: #121212; text-align:center;"
                                                        class="mobile-spacing">
                                                        <div class="mobile-br">&nbsp;</div>
                                                        <span><?= Html::encode($this->params['title']) ?></span>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if (!empty($this->params['subtitle'])) { ?>
                                                <tr>
                                                    <td style="font-size:18px; text-align:center; padding: 10px 75px 0; color:#6E6E6E;"
                                                        class="w320 mobile-spacing mobile-padding">
                                                        <span><?= $this->params['subtitle'] ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                        <table cellspacing="0" cellpadding="0" width="100%">
                                            <tr>
                                                <td>
                                                    <?php if (!empty($this->params['image_link'])) { ?>
                                                    <img src="<?= $this->params['image_link'] ?>"
                                                         style="max-width:100%; display:block;">
                                                </td>
                                                <?php } ?>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table cellspacing="0" cellpadding="0" class="force-full-width" bgcolor="#ffffff">
                                <tr>
                                    <td style="background-color:#ffffff;">
                                        <center>
                                            <center>
                                                <table style="margin: 0 auto;" cellspacing="0" cellpadding="0"
                                                       class="force-width-80">
                                                    <tr>
                                                        <td style="text-align:left; color: #6f6f6f;">
                                                            <br/>
                                                            <p>
                                                                <?= $content ?>
                                                            </p>
                                                            <br/>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </center>
                                        </center>
                                        <?php if (!empty($this->params['button_label']) && !empty($this->params['button_link'])) { ?>
                                            <table style="margin:0 auto;" cellspacing="0" cellpadding="10" width="100%">
                                                <tr>
                                                    <td style="text-align:center; margin:0 auto;">
                                                        <br/>
                                                        <div>
                                                            <!--[if mso]>
                                                            <v:rect xmlns:v="urn:schemas-microsoft-com:vml"
                                                                    xmlns:w="urn:schemas-microsoft-com:office:word"
                                                                    href="http://"
                                                                    style="height:45px;v-text-anchor:middle;width:240px;"
                                                                    stroke="f" fillcolor="#f5774e">
                                                                <w:anchorlock/>
                                                                <center>
                                                            <![endif]-->
                                                            <a class="btn" href="<?= $this->params['button_link'] ?>"
                                                               style="background-color:#607D8B;color:#ffffff;display:inline-block;font-family:'Source Sans Pro', Helvetica, Arial, sans-serif;font-size:18px;font-weight:400;line-height:45px;text-align:center;text-decoration:none;width:240px;-webkit-text-size-adjust:none;
                                                                       -webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;"><?= $this->params['button_label'] ?></a>
                                                            <!--[if mso]>
                                                            </center>
                                                            </v:rect>
                                                            <![endif]-->
                                                        </div>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                        <?php if (false) { ?>
                                            <table style="margin:0 auto;" cellspacing="0" cellpadding="10" width="100%">
                                                <tr>
                                                    <td style="text-align:center; margin:0 auto;">
                                                        <br/>
                                                        <div>
                                                            <!--[if mso]>
                                                            <v:rect xmlns:v="urn:schemas-microsoft-com:vml"
                                                                    xmlns:w="urn:schemas-microsoft-com:office:word"
                                                                    href="http://"
                                                                    style="height:45px;v-text-anchor:middle;width:240px;"
                                                                    stroke="f" fillcolor="#25D366">
                                                                <w:anchorlock/>
                                                                <center>
                                                            <![endif]-->
                                                            <a class="btn"
                                                               href="https://api.whatsapp.com/send?phone=9616432620&text=Contact%20us%20via%20whatsapp"
                                                               style="background-color:#25D366;color:#ffffff;display:inline-block;font-family:'Source Sans Pro', Helvetica, Arial, sans-serif;font-size:18px;font-weight:400;line-height:45px;text-align:center;text-decoration:none;width:240px;-webkit-text-size-adjust:none;
                                                                       -webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;">Whatsapp</a>
                                                            <!--[if mso]>
                                                            </center>
                                                            </v:rect>
                                                            <![endif]-->
                                                        </div>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table cellspacing="0" cellpadding="0" width="600" class="w320"
                       style="border-radius: 4px;overflow: hidden;">
                    <tr>
                        <td align="center" valign="top">
                            <table cellspacing="0" cellpadding="0" class="force-full-80" style="width:80%;margin:auto">
                                <tr>
                                    <td style="text-align:center;">
                                        &nbsp;
                                </tr>
                                <tr>
                                    <td style="color:#C9C9C9;;color:rgba(255,255,255,0.7); font-size: 14px;padding-bottom:4px;">
                                        <table border="0" align="left" cellpadding="0" cellspacing="0"
                                               style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                               class="force-width-50 w100p">
                                            <tr>
                                                <!--                                                        <td style="text-decoration:underline;height:30px;text-align:left" class="mobile-center">
                                                                                                            <span>Update subscription preferences</span>
                                                                                                        </td>-->
                                            </tr>
                                        </table>
                                        <table border="0" align="right" cellpadding="0" cellspacing="0"
                                               style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                               class="force-width-50 w100p">
                                            <tr>
                                                <!--                                                        <td style="text-decoration:underline;height:30px;text-align:right" class="mobile-center">
                                                                                                            <span>Unsubscribe from this list</span>
                                                                                                        </td>-->
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table cellspacing="0" cellpadding="0" class="force-full-80" style="width:80%;margin:auto">
                                <tr>
                                    <td style="text-align:center;">
                                        &nbsp;
                                </tr>
                                <tr>
                                    <td style="color:#A8A8A8;color:rgba(255,255,255,0.5); font-size: 14px;padding-bottom:4px;">
                                        <table border="0" align="center" cellpadding="0" cellspacing="0"
                                               style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                               class="force-width-50">
                                            <tr>
                                                <td style="color:#A8A8A8;height:21px;text-align:center;font-size:12px;"
                                                    class="mobile-center">
                                                    <span style="color:#A8A8A8;"><?= \Yii::t("app", "Copyright") ?> © <?= date("Y") ?> <?= Yii::$app->params['project-name'] ?>, <?= \Yii::t("app", "All Right Reserved.") ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color:#A8A8A8;height:21px;text-align:center;font-size:12px;"
                                                    class="mobile-center">
                                                    <!--<span style="color:#A8A8A8;">Jawharat Sabsabi blg, 24 st, Tripoli, Lebanon</span>-->
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px;">
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table cellspacing="0" cellpadding="0" class="force-full-width">
                    <tr>
                        <td style="font-size:12px;">
                            &nbsp;
                            <br/>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
