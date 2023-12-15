<?php


/* @var $this View */

use yii\web\View;

/* @var $maintenance_id mixed */
/* @var $form mixed */
/* @var $data mixed */
/* @var $location \common\models\Location */
/* @var $customerName mixed */
/* @var $customerSignature mixed */
/* @var $technicianName mixed */
/* @var $technicianSignature mixed */
/* @var $month mixed */
/* @var $year mixed */
/* @var $notes mixed */
/* @var $atl_notes mixed */
/* @var $maintenance \common\models\Maintenance */
$breakPointCount = 35;
switch ($form){
    case "E":
    case "F":
    case "C":
        $breakPointCount = 16;break;
    case "D":
        $breakPointCount = 19;break;
}

$formName = \common\models\MaintenanceReport::getFormName($form);
?>

<?php if ($maintenance->equipment->location->is_restricted) { ?>
    <img src="<?= $maintenance->hard_copy_report_path ?>"  style="width:193mm;max-height: 280mm"/>
<?php } else{ ?>
    <div class="center" style="position: absolute;bottom: 1mm; left: 0; width:100%;">
        <span class="small blue">
            Hotline for emergencies and service calls: <span class="blue">961-4-542801</span>
        </span>
    </div>
    <table width="100%" style=" overflow: hidden; height: 100%">
    <tbody>
    <tr>
        <td colspan="3">
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
        </td>
    </tr>
    <tr>
        <td class="" style="width: 50%;vertical-align: top;">

            <table style="width: 100%" class="all-border-bottom" cellspacing="0">
                <tbody>
                <tr class="">
                    <td class="blue bold" colspan="3">
                        <span class="en large">
                            Maintenance Service - <?= $maintenance->equipment->unit_type_label ?>
                        </span>
                    </td>
                </tr>
                <tr class="">
                    <td class="" colspan="3">
                        <span class="small bold">LS No.</span>
                        &nbsp;&nbsp;
                        <span class="small ">
                            <?= $location->code ?>
                        </span>
                    </td>
                </tr>
                <tr class="">
                    <td class="">
                        <span class="small bold">Date</span>
                        &nbsp;&nbsp;
                        <span class="small ">
                            <?= date("d", strtotime($maintenance->completed_at)) ?>
                        </span>
                        <span class="small">
                            /
                        </span>
                        <span class="small ">
                            <?= date("m", strtotime($maintenance->completed_at)) ?>
                        </span>
                        <span class="small">
                            /
                        </span>
                        <span class="small ">
                            <?= date("Y", strtotime($maintenance->completed_at)) ?>
                        </span>
                    </td>
                    <td style="border-bottom: none;width: 1mm">&nbsp;</td>
                    <td class="">
                        <span class="small bold">Zone</span>
                        &nbsp;&nbsp;
                        <span class="small "><?= $location->sector->code ?></span>
                    </td>
                </tr>
                </tbody>
            </table>

        </td>
        <td>&nbsp;</td>
        <td class="" style="width: 50%;vertical-align: top;">
            <table style="width: 100%" class="all-border-bottom" cellspacing="0">
                <tbody>
                <tr class="">
                    <td class="blue bold" colspan="1">
                        <span class="en large"><?= $maintenance_id ?></span>
                    </td>
                    <td class="blue bold right" colspan="1">
                        <span style="color: #fafafa"><?= $form ?></span>
                    </td>
                </tr>
                <tr class="">
                    <td class="" colspan="2">
                        <span class="small bold">Project</span>

                        <div class="small" style="position:absolute; top: 32.5mm;right:8mm; width: 53mm;height: 4.2mm; overflow: auto;white-space:nowrap;">
                            <?= substr($location->name, 0, 30) ?>
                        </div>
                    </td>
                </tr>
                <tr class="">
                    <td class="" colspan="2">
                        <span class="small bold">Address</span>
                        &nbsp;&nbsp;
                        <span class="small ">
                            <?= $location->address ?>
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td class="" colspan="3">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td class="" style="width: 50%;vertical-align: top;">
            <table style="width: 100%" class="" cellspacing="0">
                <tbody>

                <?php
                $empty = 6;
                $header = '<tr><td></td><td class="small border-bottom bold">Unit</td>';
                foreach (array_values(array_values($data)[0])[0] as $code => $val) {
                    $empty--;
                    $alpha = "";//str_replace($maintenance->equipment->location->code, '', $maintenance->equipment->code);
                    //substr(explode(":", $code)[0], -1);
                    $visit = \explode(":", $code)[1] == "B" ? '2nd visit' : '1st visit';
                    $header .= "<td class='center small border-bottom bold'>
                                {$alpha}<br/>
                                <span style='font-size: 1.1mm;display: block'>{$visit}</span>
                                </td>";
                }
                //$header .= "<td class='border-bottom' colspan='{$empty}'></td>";
                $header .= '</tr>' ?>

                <?= $header ?>
                <?php
                $totalSubcategories = 0;
                foreach ($data as $category => $datum) {
                    if ($totalSubcategories + count($datum) > $breakPointCount) {
                        $diff = $breakPointCount - $totalSubcategories;
                        for ($j = 0; $j < $diff; $j++) {
                            echo "<tr><td colspan='3'>&nbsp;</td></tr>";
                        }
                        $totalSubcategories = -1000;
                        echo '</tbody></table></td><td>&nbsp;</td><td class="" style="width: 50%;vertical-align: top;">
                            <table style="width: 100%" class="" cellspacing="0">
                            <tbody>' . $header;
                    }
                    $totalSubcategories = $totalSubcategories + count($datum);
                    ?>
                    <tr>
                        <td rowspan="<?= count($datum) + 1 ?>" class="border-bottom-dotted left medium"
                            style="border-right: 2px solid black; width: 0.05mm;vertical-align: bottom;text-align: center">
                            <table width="0.5mm" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td text-rotate="90" class="small left bold">
                                        &nbsp;
                                        <?= explode(":", $category)[1] ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="" colspan="2" style="width: 10mm; white-space: nowrap;height: 0.0000001mm;padding: 0mm">
                        </td>
                    </tr>
                    <?php
                    $countSubs = count($datum);
                    $subIndex = 0;
                    ?>
                    <?php foreach ($datum as $subcategory => $equipments) { ?>
                        <?php if(empty(explode(":", $subcategory)[1])) continue; ?>
                        <?php $subIndex++;
                        if ($subIndex == $countSubs) { ?>
                            <tr>
                                <td class="border-top border-right padding-left medium border-bottom-dotted padding-v">
                                    <?= explode(":", $subcategory)[1] ?>
                                </td>
                                <?php $empty = 6; ?>
                                <?php foreach ($equipments as $equipmentCode => $equipment) {
                                    $empty--; ?>
                                    <td class="border-top border-right blue medium center border-bottom-dotted" style="width: 6mm;">
                                        <?= !empty($equipment) ? '<div class="check"></div>' : '&nbsp;' ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td class="<?= $subIndex == 1 ? "" : "border-top" ?> border-right padding-left medium padding-v">
                                    <?= explode(":", $subcategory)[1] ?>
                                </td>
                                <?php $empty = 6; ?>
                                <?php foreach ($equipments as $equipmentCode => $equipment) {
                                    $empty--; ?>
                                    <td class="<?= $subIndex == 1 ? "" : "border-top" ?> border-right blue medium center" style="width: 6mm;">
                                        <?= !empty($equipment) ? '<div class="check"></div>' : '&nbsp;' ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <?php if ($totalSubcategories > 0 && $totalSubcategories < $breakPointCount) {
                    $diff = $breakPointCount - $totalSubcategories;
                    for ($j = 0; $j < $diff; $j++) {
                        echo "<tr><td colspan='3'>&nbsp;</td></tr>";
                    }
                } ?>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td class="" colspan="3">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td class="border-bottom small" colspan="3">
            <span class=" medium bold">Technician's Further Notes:</span>
            <span class="small ar">&nbsp;<?= substr($notes, 0, 320) ?></span>
        </td>
    </tr>
    <?php if (!empty($atl_notes)) { ?>
        <tr>
            <td class="border-bottom small" colspan="3">
                <span class=" medium bold">Team Leader Note:</span>
                <span class="small ar">&nbsp;<?= substr($atl_notes, 0, 320) ?></span>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td class="" colspan="3">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td class="" style="width: 50%">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td class="small bold">
                        Client Representative
                    </td>
                    <td class="small ar right">
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td>&nbsp;</td>
        <td class="" style=" width: 50%">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td class="small bold">
                        E-Maintain Representative
                    </td>
                    <td class="small ar right">
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td class="" style="width: 50%">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td class="small">
                        Name
                    </td>
                    <td class="small ar right">
                        Signature
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td>&nbsp;</td>
        <td class="" style=" width: 50%">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td class="small">
                        Technician Name
                    </td>
                    <td class="small ar right">
                        Signature
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td class="border-bottom" style="width: 50%;padding-top: 0px">
            <table width="100%">
                <tr>
                    <?php if (!$maintenance->equipment->location->is_restricted) { ?>
                        <td>
                            <span class="blue ar"><?= $customerName ?></span>
                        </td>
                        <td class="right">
                            <img src="<?= $customerSignature ?>" height="10mm"/>
                        </td>
                    <?php } else { ?>
                        <td colspan="2">
                            <span class="blue ar">Check hard copy attached below</span>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        </td>
        <td>&nbsp;</td>
        <td class="border-bottom" style=" width: 50%;padding-top: 0px">
            <table width="100%">
                <tr>
                        <td>
                            <span class="blue ar"><?= $technicianName ?></span>
                        </td>
                    <?php if (!$maintenance->equipment->location->is_restricted) { ?>
                        <td class="right">
                            <img src="<?= $technicianSignature ?>" height="10mm"/>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<?php } ?>
