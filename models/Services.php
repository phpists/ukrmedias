<?php

namespace app\models;

use \Yii;

class Services {

    const ID_COOL_WATER = 1;
    const ID_HOT_WATER = 2;
    const ID_HEAT = 3;

    static protected $client_ids;
    static public $labels = [
        self::ID_COOL_WATER => 'Холодна вода',
        self::ID_HOT_WATER => 'Гаряча вода',
        self::ID_HEAT => 'Теплоенергія',
    ];
    static public $icons = [
        self::ID_COOL_WATER => 'service_icon_1.svg',
        self::ID_HOT_WATER => 'service_icon_2.svg',
        self::ID_HEAT => 'service_icon_3.svg',
    ];
    static public $units = [
        self::ID_COOL_WATER => 'куб.м',
        self::ID_HOT_WATER => 'куб.м',
        self::ID_HEAT => 'Гкал',
    ];
    static public $unitsAlt = [
        self::ID_COOL_WATER => 'куб.м',
        self::ID_HOT_WATER => 'куб.м',
        self::ID_HEAT => 'у.о.',
    ];

    static public function set($client_id, $service_id) {
        return is_numeric(Yii::$app->db->createCommand('INSERT IGNORE INTO clients_services (client_id,service_id) VALUES(:client_id,:service_id)', [':client_id' => $client_id, ':service_id' => $service_id])->execute());
    }

    static public function unsetOne($client_id, $service_ids) {
        return is_numeric(Yii::$app->db->createCommand()->delete('clients_services', ['AND', 'client_id=:client_id', ['NOT IN', 'service_id', $service_ids]], [':client_id' => $client_id])->execute());
    }

    static public function unsetAll($client_id) {
        return is_numeric(Yii::$app->db->createCommand('DELETE FROM clients_services WHERE client_id=:client_id', [':client_id' => $client_id])->execute());
    }

    static public function getIdsByClient($client_ids) {
        if (self::$client_ids === null) {
            self::$client_ids = Yii::$app->db->createCommand('SELECT service_id FROM clients_services WHERE client_id IN(' . implode(',', $client_ids) . ')')->queryColumn();
        }
        return self::$client_ids;
    }

    static public function getIcon($sid) {
        return file_get_contents(Yii::getAlias('@app/modules/client/views/layouts/' . self::$icons[$sid]));
    }

}
