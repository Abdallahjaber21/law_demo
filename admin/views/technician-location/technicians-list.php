<?php



use common\models\TechnicianLocation;
use common\widgets\dashboard\PanelBox;
use yii\db\ActiveRecord;
use yii\web\View;

/* @var $this View */
/* @var $title string */
/* @var $type string */
/* @var $technicians null|\common\models\Technician[] */
?>

<?php $panel = PanelBox::begin([
    'title' => $title,
    'icon'  => 'table',
    'color' => PanelBox::COLOR_GRAY
]);
?>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th></th>
            <th>Personnel no.	</th>
            <th>Name</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($technicians as $index => $technician) { ?>
            <tr>
                <td><?= $index+1 ?></td>
                <td>
                    <img src="<?= $technician->image_thumb_url ?>" width="30"/>
                </td>
                <td><?= $technician->code ?></td>
                <td style="width: 50%"><?= $technician->name ?></td>
                <td><?= \yii\helpers\Html::a('View', ['technician/view','id'=>$technician->id]) ?></td>
                <td>
                    <?php if($type === 'online'){ ?>
                        <?= \yii\helpers\Html::a('View On Map', ['technician-location/map','technician_id'=>$technician->id]) ?>
                    <?php } ?>
                    <?php if($type === 'offline'){ ?>
                        <?php if(!empty($technician->technicianLocations)){ ?>
                        <?= \yii\helpers\Html::a('Last Location', ['technician-location/map','technician_id'=>$technician->id]) ?>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php PanelBox::end() ?>
