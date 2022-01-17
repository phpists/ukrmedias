<?php

namespace app\models;

use Yii;

class PreOrdersRuntimeData {

    static protected $data = [];

    static public function clean() {
        self::$data = [];
    }

    static public function add($key, $value) {
        self::$data[$key] = $value;
    }

    static public function save() {
        Yii::$app->session->setFlash(__CLASS__, self::$data);
    }

    static public function get() {
        return (array) Yii::$app->session->getFlash(__CLASS__);
    }

}
