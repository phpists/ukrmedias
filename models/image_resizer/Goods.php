<?php

namespace app\models\image_resizer;

class Goods extends \yii\base\BaseObject {

    public $original;
    public $target;
    public $width;
    public $height;
    public $crop;
    public $canvas;
    public $watermark;
    public $size;
    protected $im;
    protected $f = \Imagick::FILTER_MITCHELL;

    public function init() {
        if ($this->original === null || !is_file($this->original)) {
            throw new \Exception("File {$this->original} not exists.");
        }
        $this->im = new \Imagick($this->original);
    }

    public function save() {
        switch ($this->size):
            case 'big':
            case 'small':
                $this->variant1();
                break;
            case 'medium':
                $this->variant2();
                break;
            default:
                $this->variant0();
        endswitch;
        $this->im->setImageCompressionQuality(85);
        return $this->im->writeImage($this->target);
    }

    protected function variant1() {
        $k = $this->im->getImageHeight() / $this->im->getImageWidth();
        if ($k > 1) {
            $this->im->resizeImage($this->height / $k, $this->height, $this->f, 1);
        } elseif ($k < 1) {
            $this->im->resizeImage($this->width, $this->width * $k, $this->f, 1);
        } else {//square
            if ($this->width > $this->height) {
                $this->im->resizeImage($this->width, $this->width, $this->f, 1);
            } else {
                $this->im->resizeImage($this->height, $this->height, $this->f, 1);
            }
        }
    }

    protected function variant2() {
        $k = $this->im->getImageHeight() / $this->im->getImageWidth();
        if ($k > 1.35) {
            $this->im->resizeImage($this->height / $k, $this->height, $this->f, 1);
        } elseif ($k > 1.334 && $k <= 1.35) {
            $this->im->resizeImage($this->width, $this->width * $k, $this->f, 1);
            $this->im->cropThumbnailImage($this->width, $this->height);
        } elseif ($k > 1.3 && $k <= 1.334) {
            $this->im->resizeImage($this->height / $k, $this->height, $this->f, 1);
            $this->im->cropThumbnailImage($this->width, $this->height);
        } else {
            $this->im->resizeImage($this->width, $this->width * $k, $this->f, 1);
        }
    }

    protected function variant0() {
        if ($this->crop && ($this->im->getImageWidth() > $this->width || $this->im->getImageHeight() > $this->height)) {
            $this->im->cropThumbnailImage($this->width, $this->height);
        } elseif ($this->im->getImageWidth() > $this->width || $this->im->getImageHeight() > $this->height) {
            $this->im->resizeImage($this->width, $this->height, $this->f, 1);
        }
    }

}
