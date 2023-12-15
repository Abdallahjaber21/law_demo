<?php


/* @var $this View */

use common\models\LineItem;
use common\models\RepairRequest;
use yii\web\View;

/* @var $model RepairRequest */
$type = $model->type == RepairRequest::TYPE_REQUEST ? 'Repair' : 'Work';
$typear = $model->type == RepairRequest::TYPE_REQUEST ? 'تصليح' : 'أعمال';
$lineitem = $model->getLineItems()->where(['type' => LineItem::TYPE_TECHNICIAN])->orderBy(['id' => SORT_DESC])->one();
$atllineitem = $model->getLineItems()->where(['type' => LineItem::TYPE_ATL])->orderBy(['id' => SORT_DESC])->one();
?>

<?php if ($model->equipment->location->is_restricted) { ?>
    <img src="<?= $model->hard_copy_report_path ?>" style="width: 134mm;max-height: 193mm" />
<?php } else{ ?>
    <table width="100%" style="">
        <tbody>
        <tr>
            <td style="text-align: left;vertical-align: middle;">
                <img src="<?= Yii::getAlias("@static/images/logo.png") ?>" width="35mm"/>
            </td>
            <td class="blue small bold" style="text-align: left;vertical-align: middle; width: 40%">
                E-Maintain Lebanon S.A.L.<br/>
                E-Maintain, Mina, Tripoli<br/>
                North Lebanon
            </td>
            <td class="blue small bold" style="text-align: left;vertical-align: middle; width: 25%">
                +961 70 863 850<br/>
                +961 3 525 274<br/>
                www.emaintain.com<br/>
            </td>
        </tr>
        </tbody>
    </table>
    <br/>
    <br/>
    <table width="100%" style=" overflow: hidden">
    <tbody>
    <?php if (false) { ?>
        <tr>
            <td class="" style="width: 50%">

            </td>
            <td class="small" style="text-align: left;vertical-align: top; width: 50%">
                Hotline for emergencies and service calls:<br/>
                <span class="blue">961-4-542801</span>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td class="" style="width: 50%;vertical-align: top; padding-bottom: 0">
            <table style="width: 98%" class="all-border-bottom" cellspacing="0">
                <tbody>
                <tr class="">
                    <td class="blue bold" colspan="2" style="padding: 0;padding-top: 1.5mm">
                        <table style="width: 100%" class="no-border-bottom">
                            <tr>
                                <td class="blue bold" style="width:100%;padding: 0">
                                    <span class="en large"><?= $type ?> Service</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="">
                    <td class="">
                        <span class="small bold">Project</span>
                    </td>
                    <td class="center" nowrap="nowrap" style="white-space: nowrap;">
                        <div class="small">
                            &nbsp;
                        </div>
                        <div class="small" style="position:absolute; top: 45mm;left:20mm; width: 53mm;height: 4.2mm; overflow: auto;white-space:nowrap;">
                            <?= substr($model->equipment->location->name, 0, 30) ?>
                        </div>
                    </td>
                </tr>
                <tr class="">
                    <td class="" style="width: 19mm">
                        <span class="small bold">Contract No.</span>
                    </td>
                    <td class="">
                        <span class="small">
                            <?= $model->equipment->location->code ?>
                        </span>
                    </td>
                </tr>
                <tr class="">
                    <td class="">
                        <span class="small bold">Zone</span>
                    </td>
                    <td class="">
                        <span class="small">
                            <?= $model->equipment->location->sector->code ?>
                        </span>
                    </td>
                </tr>
                <?php if (false) { ?>
                    <tr class="">
                        <td class="">
                            <span class="small bold">Address</span>
                        </td>
                        <td class="center">
                        <span class="small red" style="white-space: nowrap;">
                            <?= $model->equipment->location->address ?>
                        </span>
                        </td>
                        <td class="right">
                            <span class="ar small">العنوان</span>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </td>
        <td class="" style="width: 50%;vertical-align: top;padding-bottom: 0;text-align: right">
            <table style="width: 98%;text-align: left" class="all-border-bottom" cellspacing="0">
                <tbody>
                <tr class="">
                    <td class="blue bold" colspan="2" style="padding: 0;padding-top: 1.5mm">
                        <table style="width: 100%" class="no-border-bottom">
                            <tr>
                                <td class="blue bold" style="width:100%;padding: 0">
                                    <span class="en large blue"><?= $model->id ?></span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="">
                    <td class="">
                        <span class="small bold">Date</span>
                    </td>
                    <td class="">
                        <span class="small ">
                            <?= date("d", strtotime($model->departed_at)) ?>
                        </span>
                        <span class="small">
                            /
                        </span>
                        <span class="small ">
                            <?= date("m", strtotime($model->departed_at)) ?>
                        </span>
                        <span class="small">
                            /
                        </span>
                        <span class="small ">
                            <?= date("Y", strtotime($model->departed_at)) ?>
                        </span>
                    </td>
                </tr>
                <tr class="">
                    <td class="">
                        <span class="small bold">Arrival time</span>
                    </td>
                    <td class="">
                        <span class="small ">
                            <?= date("H:i", strtotime($model->arrived_at . ' UTC')) ?>
                        </span>
                    </td>
                </tr>
                <tr class="">
                    <td class="" style="width: 22mm">
                        <span class="small bold">Departure time</span>
                    </td>
                    <td class="">
                        <span class="small ">
                            <?= date("H:i", strtotime($model->departed_at . ' UTC')) ?>
                        </span>
                    </td>
                </tr>
                <?php if (false) { ?>
                    <tr class="">
                        <td class="">
                            <span class="small bold">No. units checked</span>
                        </td>
                        <td class="center">
                        <span class="small red">
                            1
                        </span>
                        </td>
                        <td class="right">
                            <span class="ar small" style="font-size: 1.5mm">عدد المصاعد</span><br/>
                            <span class="ar small" style="font-size: 1.5mm">اللتي تم فحصها</span>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 0">
            <table style="width: 100%;" class="all-border-bottom" cellspacing="0">
                <tbody>
                <tr class="">
                    <td class="" style="width: 19mm">
                        <span class="small bold">Address</span>
                    </td>
                    <td class="">
                        <span class="small " style="white-space: nowrap;">
                            <?= $model->equipment->location->address ?>
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td colspan="2">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td class="" style="width: 50%;vertical-align: top;">
            <table style="width: 98%" class="" cellspacing="0">
                <tbody>
                <tr>
                    <td rowspan="2" class="border-bottom-dotted" style="border-right: 2px solid black"></td>
                    <td class="border-v padding-left medium">Unit</td>
                    <td class="border-all  center" style="width: 6mm;">
                        <?= "";//str_replace($model->equipment->location->code, '', $model->equipment->code) ?>
                    </td>
                    <td class="border-v" style="width: 6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td text-rotate="90" rowspan="4" class="border-bottom-dotted center medium bold"
                        style="border-right: 2px solid black; width: 1px;padding: 5px;vertical-align: middle">
                        Intervention
                    </td>
                    <td colspan="3" style="width: 1mm; white-space: nowrap">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td style="width: 1mm; white-space: nowrap">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                    <td class="" style="width: 6mm;">&nbsp;</td>
                    <td class="" style="width: 6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td class="border-v padding-left medium">Breakdown</td>
                    <td class="border-all  center" style="width: 6mm;">
                        <?= $model->type == RepairRequest::TYPE_REQUEST ? "Y" : "N" ?>
                    </td>
                    <td class="border-v" style="width: 6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td class=" padding-left medium border-bottom-dotted">Works</td>
                    <td class="border-right  center border-bottom-dotted" style="width: 6mm;">
                        <?= $model->type == RepairRequest::TYPE_SCHEDULED ? "Y" : "N" ?>
                    </td>
                    <td class=" border-bottom-dotted" style="width: 6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td text-rotate="90" rowspan="6" class="border-bottom-dotted center medium bold"
                        style="border-right: 2px solid black; width: 1px;padding: 5px;vertical-align: middle">
                        Code
                    </td>
                    <td colspan="3" style="width: 1mm; white-space: nowrap">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td class="border-v padding-left medium">Chapter</td>
                    <td class="border-all  center" style="width: 6mm;">
                        <?= $lineitem->objectCode->objectCategory->code ?>
                    </td>
                    <td class="border-v blue center" style="width: 6mm;">
                        <?php if (!empty($atllineitem)) { ?>
                            <?= $atllineitem->objectCode->objectCategory->code ?>
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class=" border-bottom padding-left medium">Error</td>
                    <td class="border-right border-bottom  center" style="width: 6mm;">
                        <?= $lineitem->objectCode->code ?>
                    </td>
                    <td class=" border-bottom blue center" style="width: 6mm;">
                        <?php if (!empty($atllineitem)) { ?>
                            <?= $atllineitem->objectCode->code ?>
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class=" border-bottom padding-left medium">Cause</td>
                    <td class="border-right border-bottom  center" style="width: 6mm;">
                        <?= $lineitem->causeCode->code ?>
                    </td>
                    <td class=" border-bottom blue center" style="width: 6mm;">
                        <?php if (!empty($atllineitem)) { ?>
                            <?= $atllineitem->causeCode->code ?>
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class=" border-bottom padding-left medium">Reason</td>
                    <td class="border-right border-bottom  center" style="width: 6mm;">
                        <?= $lineitem->damageCode->code ?>
                    </td>
                    <td class=" border-bottom blue center" style="width: 6mm;">
                        <?php if (!empty($atllineitem)) { ?>
                            <?= $atllineitem->damageCode->code ?>
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class=" padding-left medium border-bottom-dotted">Made By</td>
                    <td class="border-right  center border-bottom-dotted" style="width: 6mm;">
                        <?= $lineitem->manufacturer->code ?>
                    </td>
                    <td class=" border-bottom-dotted blue center" style="width: 6mm;">
                        <?php if (!empty($atllineitem)) { ?>
                            <?= $atllineitem->manufacturer->code ?>
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                    </td>
                </tr>


                <tr>
                    <td text-rotate="90" rowspan="6" class="border-bottom-dotted center medium bold"
                        style="border-right: 2px solid black; width: 1px;padding: 5px;vertical-align: middle">
                        Status
                    </td>
                    <td colspan="4" style="width: 1mm; white-space: nowrap">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td class="border-v padding-left medium">Pending</td>
                    <td class="border-all  center" style="width: 6mm;">
                        <?= $model->system_operational ? "N" : "Y" ?>
                    </td>
                    <td class="border-v" style="width: 6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td class=" padding-left medium border-bottom-dotted">Running</td>
                    <td class="border-right  center border-bottom-dotted" style="width: 6mm;">
                        <?= $model->system_operational ? "Y" : "N" ?>
                    </td>
                    <td class=" border-bottom-dotted" style="width: 6mm;">&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </td>

        <td class="" style="width: 50%;vertical-align: top;text-align: right">
            <div style="background:rgba(55,55,55,0);position:absolute; top: 80.5mm;right:9mm; line-height: 5.4mm; width: 62.5mm; overflow: visible;white-space: normal" class=" ">
                <div class="bold" style="text-align: left">Technician's Further Notes</div>
                <div class="medium ar">
                    <?php
                    $note = $model->note_client;
                    $note = preg_replace("/([a-zA-Z0-9]+)/", '<span class="en">$1</span>',$note);
                    ?>
                    <?= $note ?>
                </div>
            </div>

            <?php if (!empty($model->atl_note)) { ?>
                <div style="background:rgba(55,55,55,0);position:absolute; top: 123mm;right:9mm; line-height: 5.4mm; width: 62.5mm; overflow: visible;white-space: normal" class="">
                    <div class="bold" style="text-align: left">Team Leader Note</div>
                    <div class="medium ar">
                        <?php
                        $atl_note = $model->atl_note;
                        $atl_note = preg_replace("/([a-zA-Z0-9]+)/", '<span class="en">$1</span>',$atl_note);
                        ?>
                        <?= $atl_note ?>
                    </div>
                </div>
            <?php } ?>
            <table style="width: 98%; overflow: hidden;text-align: left" class="" cellspacing="0">
                <tbody>

                <tr>
                    <td style="width: 300px; white-space: nowrap" class="border-bottom">
                        &nbsp;
                    </td>
                </tr>
                <?php for ($i = 0; $i < 5; $i++) { ?>
                    <tr class="">
                        <td style="width: 1mm; white-space: nowrap" class="border-bottom">
                            &nbsp;
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="width: 300px; white-space: nowrap" class="border-bottom">
                        &nbsp;
                    </td>
                </tr>
                <?php for ($i = 0; $i < 8; $i++) { ?>
                    <tr class="">
                        <td style="width: 1mm; white-space: nowrap" class="border-bottom">
                            &nbsp;
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="border-bottom-dotted" style="width: 6mm;">&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td class="" style="width: 50%">
            <table style="width: 98%">
                <tbody>
                <tr>
                    <td class="bold">
                        Client Representative
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td class="" style=" width: 98%;text-align: right">
            <table style="width: 100%;text-align: left">
                <tbody>
                <tr>
                    <td class="bold">
                        E-Maintain Representative
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td class="" style="width: 50%">
            <table style="width: 98%">
                <tbody>
                <tr>
                    <td class="">
                        Name
                    </td>
                    <td class=" ar right">
                        Signature
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td class="" style=" width: 50%;text-align: right">
            <table style="width: 98%;text-align: left">
                <tbody>
                <tr>
                    <td class="">
                        Technician Name
                    </td>
                    <td class=" ar right">
                        Signature
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="width: 50%;padding-top: 0px">
            <table class="border-bottom" style="width: 98%;">
                <tr>
                    <?php if (!$model->equipment->location->is_restricted) { ?>
                        <td>
                            <span class="blue"><?= $model->customer_name ?></span>
                        </td>
                        <td class="right">
                            <?php if (!empty($model->customer_signature)) { ?>
                                <div style="position:absolute;bottom:16.725mm;left:44mm;z-index: 10; width: 28mm;">
                                    <div class="border-bottom"  style="width: 100%;;"></div>
                                </div>
                                <div style="position:absolute;bottom:11mm;left:44mm;z-index: 1;border-top: none">
                                    <img src="<?= $model->customer_signature_path ?>" height="14mm"/>
                                </div>
                            <?php } ?>
                        </td>
                    <?php } else{ ?>
                        <td colspan="2">
                            <span class="red ar">Check hard copy attached below</span>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        </td>
        <td  style=" width: 50%;padding-top: 0px;text-align: right;z-index: 10">
            <table class="border-bottom" style="width: 98%;text-align: left">
                <tr>
                    <td>
                        <span class="blue"><?= $model->technician->name ?></span>
                    </td>
                    <td class="right">
                        <?php if (!empty($model->technician_signature)) { ?>
                            <div style="position:absolute;bottom:16.725mm;right:8mm;z-index: 10; width: 28mm;">
                                <div class="border-bottom"  style="width: 100%;;"></div>
                            </div>
                            <div style="position:absolute;bottom:11mm;right:8mm;z-index: 1;border-top: none">
                                <img src="<?= $model->technician_signature_path ?>" height="14mm"/>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<?php } ?>
