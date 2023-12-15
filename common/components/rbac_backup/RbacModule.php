<?php

namespace common\components\rbac;

use Yii;
use yii\base\Exception;
use yii\base\Module;

/**
 * Description of RbacModule
 *
 * @author Tarek K. Ajaj
 */
class RbacModule extends Module {

    /**
     *
     * @var string $userModelClassName The user model class.
     * Default it will get from `Yii::$app->getUser()->identityClass`
     */
    public $userModelClassName;

    /**
     *
     * @var string $userModelIdField the id field name of user model.
     * Default is id
     */
    public $userModelIdField = 'id';

    /**
     *
     * @var string $userModelLoginField the login field name of user model.
     * Default is username
     */
    public $userModelLoginField = 'name';

    /**
     *
     * @var string $userModelLoginFieldLabel The login field's label of user model.
     * Default is Username  
     */
    public $userModelLoginFieldLabel;

    /**
     * Initilation module
     * @return void
     */
    public function init() {
        parent::init();
        if ($this->userModelClassName == null) {
            if (Yii::$app->has('user')) {
                $this->userModelClassName = Yii::$app->user->identityClass;
            } else {
                throw new Exception("You must config user compoment both console and web config");
            }
        }
        if ($this->userModelLoginFieldLabel == null) {
            $model = new $this->userModelClassName;
            $this->userModelLoginFieldLabel = $model->getAttributeLabel($this->userModelLoginField);
        }
    }

}
