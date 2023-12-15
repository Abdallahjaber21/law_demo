<?php

namespace common\components\extensions;

use yeesoft\multilingual\helpers\MultilingualHelper;
use yeesoft\multilingual\widgets\LanguageSwitcher as LanguageSwitcher2;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Description of LanguageSwitcher
 *
 * @author Tarek K. Ajaj
 */
class LanguageSwitcher extends LanguageSwitcher2 {

    const VIEW_DROPDOWN = 'dropdown';

    /**
     * @var array default views of switcher. 
     */
    protected $_reservedViews = [
        'links' => '@vendor/yeesoft/yii2-multilingual/src/views/switcher/links',
        'pills' => '@vendor/yeesoft/yii2-multilingual/src/views/switcher/pills',
        'dropdown' => '@common/components/extensions/views/dropdown-switcher',
    ];

    public function run() {
        if (count($this->languages) > 1) {
            $view = isset($this->_reservedViews[$this->view]) ? $this->_reservedViews[$this->view] : $this->view;
            list($route, $params) = Yii::$app->getUrlManager()->parseRequest(Yii::$app->getRequest());
            $params = ArrayHelper::merge(Yii::$app->getRequest()->get(), $params);
            $url = isset($params['route']) ? $params['route'] : $route;
            $module = Yii::$app->controller->module->id;
            if (!empty($module) && strpos($url, "{$module}/") === 0) {
                $url = substr($url, strlen("{$module}/"));
            }

            return $this->render($view, [
                        'url' => $url,
                        'params' => $params,
                        'display' => $this->display,
                        'language' => MultilingualHelper::getDisplayLanguageCode($this->_currentLanguage, $this->languageRedirects),
                        'languages' => MultilingualHelper::getDisplayLanguages($this->languages, $this->languageRedirects),
            ]);
        }
    }

}
