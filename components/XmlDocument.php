<?php

namespace app\components;

use yii\helpers\Html;

abstract class XmlDocument {

    public $file;
    protected $dom;

    public function __construct($file) {
        $this->dom = new \DOMDocument();
        $this->dom->version = '1.0';
        $this->dom->encoding = 'utf-8';
        $this->file = $file;
    }

    public function saveFile() {
        return file_put_contents($this->file, $this->__toString());
    }

    public function __toString() {
        return $this->dom->saveXML();
    }

//    public function getDate() {
//        return is_file($this->file) ? date('d-m-Y H:i:s', filemtime($this->file)) : null;
//    }

    protected function el($name, $text = null, $attrs = array()) {
        $element = $this->dom->createElement($name);
        foreach ($attrs as $attr => $value) {
            $element->setAttribute($attr, Html::encode($value));
        }
        if ($text !== null) {
            $element->appendChild($this->dom->createTextNode(Html::encode($text)));
        }
        return $element;
    }

    protected function cdata($name, $text = null, $attrs = array()) {
        $element = $this->dom->createElement($name);
        foreach ($attrs as $attr => $value) {
            $element->setAttribute($attr, Html::encode($value));
        }
        if ($text !== null) {
            $element->appendChild($this->dom->createCDATASection(Html::encode($text)));
        }
        return $element;
    }

    abstract static function create($file, $filter = []);
}
