<?php

namespace app\models;

use \Yii;

class ManagersFirms {

    static protected $firm_ids;

    static public function getFirmsIds($manager_id = null) {
        if ($manager_id === null) {
            $manager_id = Yii::$app->user->getId();
        }
        if (!isset(self::$firm_ids[$manager_id])) {
            self::$firm_ids[$manager_id] = Yii::$app->db->createCommand('SELECT firm_id FROM managers_firms WHERE manager_id=:id', [':id' => $manager_id])->queryColumn();
        }
        return self::$firm_ids[$manager_id];
    }

    static public function set($manager_id, $firm_id) {
        return is_numeric(Yii::$app->db->createCommand('INSERT IGNORE INTO managers_firms (manager_id,firm_id) VALUES(:manager_id,:firm_id)', [':manager_id' => $manager_id, ':firm_id' => $firm_id])->execute());
    }

    static public function unset($manager_id, $firm_ids) {
        return is_numeric(Yii::$app->db->createCommand()->delete('managers_firms', ['AND', 'manager_id=:manager_id', ['NOT IN', 'firm_id', $firm_ids]], [':manager_id' => $manager_id])->execute());
    }

    static public function unsetAll($manager_id) {
        return is_numeric(Yii::$app->db->createCommand('DELETE FROM managers_firms WHERE manager_id=:manager_id', [':manager_id' => $manager_id])->execute());
    }

    static public function unsetAlt($firm_id, $manager_ids) {
        return is_numeric(Yii::$app->db->createCommand()->delete('managers_firms', ['AND', 'firm_id=:firm_id', ['NOT IN', 'manager_id', $manager_ids]], [':firm_id' => $firm_id])->execute());
    }

    static public function unsetAllAlt($firm_id) {
        return is_numeric(Yii::$app->db->createCommand('DELETE FROM managers_firms WHERE firm_id=:firm_id', [':firm_id' => $firm_id])->execute());
    }

}
