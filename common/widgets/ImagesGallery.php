<?php

namespace common\widgets;

use common\models\Gallery;
use common\models\Image;
use yii\base\Widget;

/**
 * Description of ImagesGallery
 *
 * @author Tarek K. Ajaj
 * Feb 3, 2017 8:22:40 AM
 * 
 * ImagesGallery.php
 * UTF-8
 * 
 */
class ImagesGallery extends Widget {

    /**
     *
     * @var Gallery[]
     */
    public $gallery;

    /**
     *
     * @var Image[]
     */
    public $images;

    public function init() {
        parent::init();
        if (!isset($this->images)) {
            if (isset($this->gallery)) {
                $this->images = $this->gallery->images;
            }
        }
    }

    public function run() {
        if (count($this->images) > 0) {
            return $this->render('images-gallery', [
                        'images' => $this->images
            ]);
        }
        return 'No Images';
    }

}
