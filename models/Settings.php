<?php

namespace app\models;

use \Yii;
use app\components\Misc;

class Settings extends \app\components\BaseActiveRecord {

    const ORDER_MIN_AMOUNT = '1';
    const NOTIFY_EMAIL = '2';
    const API_URL = '3';
    const API_KEY = '4';
    const SMS_SENDER = '5';
    const SMS_TOKEN = '6';
    const NOVA_POSHTA_KEY = '7';

    public $data;
    static public $dataModels;
    static public $labels = array(
        self::ORDER_MIN_AMOUNT => 'Мінімальна сума замовлення',
        self::NOTIFY_EMAIL => 'Email для повідомлень',
        self::API_URL => 'Базовий URL веб-служби 1С',
        self::API_KEY => 'Ключ веб-служби 1С',
        self::SMS_SENDER => 'TurboSMS: отправитель',
        self::SMS_TOKEN => 'TurboSMS: токен авторизации',
        self::NOVA_POSHTA_KEY => 'Нова Пошта: ключ АПІ',
    );
    static public $sections = array(
        [self::ORDER_MIN_AMOUNT, self::NOTIFY_EMAIL],
        [self::SMS_SENDER, self::SMS_TOKEN, self::NOVA_POSHTA_KEY, self::API_URL, self::API_KEY],
    );

    public static function tableName() {
        return 'settings';
    }

    public function rules() {
        return [
            ['value', 'filter', 'filter' => 'trim'],
            ['value', 'checkit'],
            ['data', 'safe'],
        ];
    }

    public function checkit() {
        switch ($this->id):
            case self::ORDER_MIN_AMOUNT:
                $this->value = Misc::priceFilter($this->value);
                break;
            case self::NOTIFY_EMAIL:
                $validator = new \yii\validators\EmailValidator();
                if (!$validator->validate($this->value)) {
                    $this->addError('value', 'Некоректний email.');
                }
                break;
            case self::API_URL:
                $validator = new \yii\validators\UrlValidator();
                if (!$validator->validate($this->value)) {
                    $this->addError('value', 'Некоректний URL.');
                }
                break;
        endswitch;
        if (strlen($this->value) > 255) {
            $this->addError('value', 'Поле не може перевищувати 255 символів.');
        }
    }

    static public function initData() {
        if (self::$dataModels === null) {
            self::$dataModels = self::find()->indexBy('id')->all();
            foreach (array_keys(self::$labels) as $id) {
                if (!isset(self::$dataModels[$id])) {
                    self::$dataModels[$id] = new self;
                    self::$dataModels[$id]->id = $id;
                }
            }
        }
        return self::$dataModels;
    }

    static public function get($id) {
        self::initData();
        return isset(self::$dataModels[$id]) ? self::$dataModels[$id]->value : '';
    }

    static public function nl2br($id) {
        return nl2br(self::get($id));
    }

    static public function phone($id) {
        return preg_replace('/[^\d\+]+/', '', self::get($id));
    }

    public function saveModel() {
        $res = true;
        foreach (self::$dataModels as $id => $dataModel) {
            if (!isset($this->data[$id])) {
                continue;
            }
            $dataModel->setAttributes($this->data[$id]);
            $dataModel->save();
            if ($dataModel->hasErrors()) {
                $res = false;
            }
        }
        return $res;
    }

//    static public function keyval() {
//        return Yii::$app->db->getMasterPdo()->query('SELECT sid,title FROM ' . self::tableName() . ' ORDER BY id')->fetchAll(\PDO::FETCH_KEY_PAIR);
//    }
}
