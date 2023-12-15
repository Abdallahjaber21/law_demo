<?php

use common\models\users\Admin;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $user Admin */

$user = Yii::$app->getUser()->getIdentity();
?>

<?php if (!Yii::$app->getUser()->isGuest) { ?>
<li class="dropdown user user-menu">
    <?php $imagePath =  $user->image_thumb_path;
        if (file_exists($imagePath)) {
            $imageUrl = $user->image_thumb_url;
        } else {
            $imageUrl = Yii::getAlias('@staticWeb') . '/images/user-default.jpg';
        } ?>
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <img src="<?= $imageUrl ?>" class="user-image" alt="<?= $user->name ?>">
        <span class="hidden-xs"><?= $user->name ?></span> <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
            <?php $imagePath =  $user->image_thumb_path;
                if (file_exists($imagePath)) {
                    $imageUrl = $user->image_thumb_url;
                } else {
                    $imageUrl = Yii::getAlias('@staticWeb') . '/images/user-default.jpg';
                } ?>
            <img src="<?= $imageUrl ?>" class="img-circle" alt="<?= $user->name ?>">

            <p>
                <?= $user->email ?>
                <small><?= $user->address ?></small>
                <small><?= $user->phone_number ?></small>
            </p>
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                <?= Html::a(\Yii::t("app", "Edit Profile"), ['/site/profile'], ['role' => 'menuitem', 'class' => 'btn btn-default btn-flat']) ?>
            </div>
            <div class="pull-right">
                <?= Html::a(\Yii::t("app", 'Sign out'), ['/site/logout'], ['data-method' => 'post', 'role' => 'menuitem', 'class' => 'btn btn-danger btn-flat']) ?>
            </div>
        </li>
    </ul>
</li>
<?php } ?>