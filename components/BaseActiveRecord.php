<?php

namespace app\components;

use yii\helpers\Inflector;
use Yii;

abstract class BaseActiveRecord extends \yii\db\ActiveRecord {

    static public $valuesTrueFalse = array('0' => 'ні', '1' => 'так');
    public $oldAttributes = array();

    public function beforeSave($insert) {
        $this->oldAttributes = $this->getOldAttributes();
        return parent::beforeSave($insert);
    }

    public function generateAttributeLabel($name) {
        return $name;
    }

    static public function findModel() {
        $args = func_get_args();
        $a = array_shift($args);
        if (is_array($a)) {
            $model = self::find()->where($a)->one();
        } elseif ($a === null) {
            $model = null;
        } else {
            $model = self::findOne($a);
        }
        if ($model === null) {
            $model = \yii\di\Instance::ensure(get_called_class());
        }
        return $model;
    }

    public function getErrorsList() {
        $data = array();
        foreach ($this->getErrors() as $errors) {
            $data = array_merge($data, $errors);
        }
        return $data;
    }

    public function isSubmitted() {
        return $this->load(Yii::$app->request->post());
    }

    static public function updatePos($ids) {
        if (count($ids) === 0) {
            return true;
        }
        $params = [];
        $sql = 'UPDATE ' . self::tableName() . ' SET `pos` = CASE ';
        foreach ($ids as $pos => $id) {
            $key = str_replace('-', '_', ":id{$id}");
            $params[$key] = $id;
            $sql .= "WHEN id={$key} THEN {$pos} ";
        }
        $sql .= 'END WHERE `id` IN (' . implode(',', array_keys($params)) . ')';
        return self::getDb()->createCommand($sql, $params)->execute();
    }

}
