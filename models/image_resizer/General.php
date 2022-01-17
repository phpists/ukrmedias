<?php

namespace app\models\image_resizer;

class General extends \yii\base\BaseObject {

    public $original;
    public $target;
    public $width;
    public $height;
    public $crop;
    public $canvas;
    public $watermark;
    public $size;
    protected $im;

    public function init() {
        if ($this->original === null || !is_file($this->original)) {
            throw new \Exception("File {$this->original} not exists.");
        }
        $this->im = new \Imagick($this->original);
    }

    public function save() {
        if ($this->crop && ($this->im->getImageWidth() > $this->width || $this->im->getImageHeight() > $this->height)) {
            $this->im->cropThumbnailImage($this->width, $this->height);
        } elseif ($this->im->getImageWidth() > $this->width || $this->im->getImageHeight() > $this->height) {
            $this->im->resizeImage($this->width, $this->height, \Imagick::FILTER_MITCHELL, 1);
        }

        if (is_string($this->watermark) && is_file($this->watermark)) {
            $wm = new \Imagick($this->watermark);
            $wm->resizeImage(min($this->im->getImageWidth() / 5, $wm->getImageWidth()), min($this->im->getImageHeight() / 5, $wm->getImageHeight()), \Imagick::FILTER_MITCHELL, 1);
            $this->im->compositeImage($wm, $wm->getImageCompose(), $this->im->getImageWidth() - $wm->getImageWidth(), $this->im->getImageHeight() - $wm->getImageHeight());
        }
        $c = new \Imagick();
        $c->newImage($this->width, $this->height, new \ImagickPixel($this->canvas));
        $c->setImageFormat(substr($this->original, strrpos($this->original, '.') + 1));
        $paddingX = ($this->width - $this->im->getImageWidth()) / 2;
        $paddingY = ($this->height - $this->im->getImageHeight()) / 2;
        $c->setColorspace($this->im->getImageColorspace());
        $c->compositeImage($this->im, $this->im->getImageCompose(), $paddingX, $paddingY);
        $c->setImageCompressionQuality(85);
        return $c->writeImage($this->target);
    }

}
