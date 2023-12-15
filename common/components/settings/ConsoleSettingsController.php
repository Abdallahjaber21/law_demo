<?php

namespace common\components\settings;

use Yii;
use yii\console\Controller;

/**
 * Description of ConsoleSettingsController
 *
 * @author Tarek K. Ajaj
 */
class ConsoleSettingsController extends Controller {

    /**
     * Use the settings configuration file to setup the settings
     * this will automatically update existing setting labels but will keep 
     * setting type & value untouched
     */
    public function actionUpdate() {
        $configuration = Yii::$app->settings->getConfiguration();
        if (!empty($configuration)) {
            //Clear all settings hierarchy
            Setting::updateAll(['setting_category_id' => null]);
            //Start adding/updating categories
            foreach ($configuration as $category_name => $category_config) {
                $category = SettingCategory::findByName($category_name);
                if (empty($category)) {
                    $category = SettingCategory::addNew($category_name, $category_config);
                } else {
                    $category->label = $category_config['label'];
                    $category->save();
                }

                $settings = $category_config['settings'];
                if (!empty($settings) && is_array($settings)) {
                    foreach ($settings as $setting_name => $setting_config) {
                        $setting = Setting::findByName($setting_name);
                        if (empty($setting)) {
                            if (Setting::addNew($setting_name, $setting_config, $category->id)) {
                                echo "Added $setting_name \n";
                            } else {
                                echo "Error occured while adding $setting_name \n";
                            }
                        } else {
                            if ($setting->updateSetting($setting_config, $category->id)) {
                                echo "Update $setting_name \n";
                            } else {
                                echo "Error occured while updating $setting_name \n";
                            }
                        }
                    }
                }
            }
            $countDeleted = Setting::deleteAll(['setting_category_id' => null]);
            echo "Deleted $countDeleted settings";
        }
    }

}
