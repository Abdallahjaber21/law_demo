<?php

namespace common\behaviors;

use PHPThumb\GD;
use Yii;
use yii\base\UnknownPropertyException;
use yii\helpers\FileHelper;
use yiidreamteam\upload\ImageUploadBehavior as OriginalImageUploadBehavior;

/**
 * Description of ImageUploadBehavior
 *
 * @author Tarek K. Ajaj
 * May 3, 2017 4:15:32 PM
 * 
 * ImageUploadBehavior.php
 * UTF-8
 * 
 */
class ImageUploadBehavior extends OriginalImageUploadBehavior
{

    public $watermarkPath;
    public $watermarkUrl;
    public $createWatermarksOnSave = true;
    public $createWatermarksOnRequest = false;
    public $defaultUrl;
    public $defaultThumbUrl;
    public $defaultWatermarkedUrl;
    public $resize;
    public $watermarked = [];
    public $watermark;

    /**
     * - leftTop/leftop/topleft/topLeft same as westNorth same as westupper same as leftnorth or any other combination
     * - center --> centers both the x- and y-axis
     * - leftCenter --> set x-axis to the left corner of the image and centers the y-axis
     */
    public $watermarkPosition = "center";
    public $watermarkOffsetX = 0;
    public $watermarkOffsetY = 0;

    const RESIZE_CONTAIN = 10;
    const RESIZE_CROP = 20;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->watermark)) {
            $this->watermark = Yii::getAlias("@static") . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "watermark.png";
        }
        if (empty($this->defaultUrl)) {
            $this->defaultUrl = Yii::getAlias("@staticWeb") . "/images/placeholder.jpg";
        }
        if (empty($this->defaultThumbUrl)) {
            if (empty($this->defaultUrl)) {
                $this->defaultThumbUrl = Yii::getAlias("@staticWeb") . "/images/placeholder.jpg";
            } else {
                $this->defaultThumbUrl = $this->defaultUrl;
            }
        }
        if (empty($this->defaultWatermarkedUrl)) {
            if (empty($this->defaultUrl)) {
                $this->defaultWatermarkedUrl = Yii::getAlias("@staticWeb") . "/images/placeholder.jpg";
            } else {
                $this->defaultWatermarkedUrl = $this->defaultUrl;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            if ($name == $this->attribute . '_url') {
                return str_replace(" ", "", $this->getImageFileUrl($this->attribute, $this->defaultUrl));
            }
            if ($name == $this->attribute . '_path') {
                return $this->getUploadedFilePath($this->attribute);
            }
            foreach ($this->thumbs as $key => $thumb) {
                if ($name == $this->attribute . "_{$key}_url") {
                    return str_replace(" ", "", $this->getThumbFileUrl($this->attribute, $key, $this->defaultThumbUrl));
                }
                if ($name == $this->attribute . "_{$key}_path") {
                    return $this->getThumbFilePath($this->attribute, $key);
                }
            }
            foreach ($this->watermarked as $key => $watermarked) {
                if ($name == $this->attribute . "_{$key}_url") {
                    return str_replace(" ", "", $this->getWatermarkedFileUrl($this->attribute, $key, $this->defaultWatermarkedUrl));
                }
                if ($name == $this->attribute . "_{$key}_path") {
                    return $this->getWatermarkedFilePath($this->attribute, $key);
                }
            }
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if ($name == $this->attribute . '_url') {
            return true;
        }
        if ($name == $this->attribute . '_path') {
            return true;
        }
        foreach ($this->thumbs as $key => $thumb) {
            if ($name == $this->attribute . "_{$key}_url") {
                return true;
            }
            if ($name == $this->attribute . "_{$key}_path") {
                return true;
            }
        }
        foreach ($this->watermarked as $key => $watermarked) {
            if ($name == $this->attribute . "_{$key}_url") {
                return true;
            }
            if ($name == $this->attribute . "_{$key}_path") {
                return true;
            }
        }
        return false;
    }

    /**
     * Creates image thumbnails
     */
    public function createThumbs()
    {
        $path = $this->getUploadedFilePath($this->attribute);
        foreach ($this->thumbs as $profile => $config) {
            Yii::info($config, 'Batata');
            $thumbPath = static::getThumbFilePath($this->attribute, $profile);
            if (is_file($path) && !is_file($thumbPath)) {

                // setup image processor function
                if (isset($config['processor']) && is_callable($config['processor'])) {
                    $processor = $config['processor'];
                    unset($config['processor']);
                } else {
                    $processor = function (GD $thumb) use ($config) {
                        if ($this->resize == self::RESIZE_CONTAIN) {
                            $thumb->resize($config['width'], $config['height']);
                        } else {
                            $thumb->adaptiveResize($config['width'], $config['height']);
                        }
                    };
                }

                $thumb = new GD($path, $config);
                call_user_func($processor, $thumb, $this->attribute);
                FileHelper::createDirectory(pathinfo($thumbPath, PATHINFO_DIRNAME), 0775, true);
                $thumb->save($thumbPath);
            }
        }
    }

    public function createWatermarks()
    {
        $path = $this->getUploadedFilePath($this->attribute);
        foreach ($this->watermarked as $profile => $config) {
            $watermarkPath = !empty($config['watermark']) ? $config['watermark'] : $this->watermark;
            $watermarkPosition = !empty($config['position']) ? $config['position'] : $this->watermarkPosition;
            $watermarkOffsetX = !empty($config['offset-x']) ? $config['offset-x'] : $this->watermarkOffsetX;
            $watermarkOffsetY = !empty($config['offset-y']) ? $config['offset-y'] : $this->watermarkOffsetY;
            $waterkmarkedPath = static::getWatermarkedFilePath($this->attribute, $profile);
            if (is_file($path) && !is_file($waterkmarkedPath)) {

                // setup image processor function
                if (isset($config['processor']) && is_callable($config['processor'])) {
                    $processor = $config['processor'];
                    unset($config['processor']);
                } else {
                    $processor = function (GD $thumb) use ($config) {
                        if ($this->resize == self::RESIZE_CONTAIN) {
                            $thumb->resize($config['width'], $config['height']);
                        } else {
                            $thumb->adaptiveResize($config['width'], $config['height']);
                        }
                    };
                }

                $thumb = new GD($path, $config);
                call_user_func($processor, $thumb, $this->attribute);
                FileHelper::createDirectory(pathinfo($waterkmarkedPath, PATHINFO_DIRNAME), 0775, true);
                $thumb->save($waterkmarkedPath);

                $watermark = new GD($watermarkPath);
                GdWatermark::addWatermark($watermark, $watermarkPosition, $watermarkOffsetX, $watermarkOffsetY, $thumb);
                $thumb->save($waterkmarkedPath);
            }
        }
    }

    /**
     * @param string $attribute
     * @param string $profile
     * @return string
     */
    public function getWatermarkedFilePath($attribute, $profile = 'wm')
    {
        $behavior = static::getInstance($this->owner, $attribute);
        return $behavior->resolveProfilePath($behavior->watermarkPath, $profile);
    }

    /**
     * @param string $attribute
     * @param string $profile
     * @param string|null $emptyUrl
     * @return string|null
     */
    public function getWatermarkedFileUrl($attribute, $profile = 'wm', $emptyUrl = null)
    {
        if (!$this->owner->{$attribute}) {
            return $emptyUrl;
        }

        $behavior = static::getInstance($this->owner, $attribute);
        if ($behavior->createWatermarksOnRequest) {
            $behavior->createWatermarks();
        }
        return $behavior->resolveProfilePath($behavior->watermarkUrl, $profile);
    }

    /**
     * After file save event handler.
     */
    public function afterFileSave()
    {
        if ($this->createThumbsOnSave == true) {
            $this->createThumbs();
        }
        if ($this->createWatermarksOnSave == true) {
            $this->createWatermarks();
        }
    }

    /**
     * @inheritdoc
     */
    public function cleanFiles()
    {
        parent::cleanFiles();
        foreach (array_keys($this->thumbs) as $profile) {
            @unlink($this->getThumbFilePath($this->attribute, $profile));
        }
        foreach (array_keys($this->watermarked) as $profile) {
            @unlink($this->getWatermarkedFilePath($this->attribute, $profile));
        }
    }
}
