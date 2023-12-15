<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\widgets\dashboard;

use yii\base\Widget;

/**
 * Description of ProfileDropdown
 *
 * @author Tarek K. Ajaj
 */
class ProfileDropdown extends Widget {

    public function run() {
        return $this->render("profile-dropdown");
    }

}
