<?php
namespace console\migrations\namespaced;

use Yii;
use yii\db\Migration;

/**
 * Initializes RBAC data
 *
 * @author Tarek K. Ajaj
 */
class m140506_102107_rbac_setup extends Migration {

    /**
     * @inheritdoc
     */
    public function up() {
        $auth = Yii::$app->authManager;
        $developer = $auth->createRole('developer');
        $auth->add($developer);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('developer');
        $auth->remove($role);
    }

}
