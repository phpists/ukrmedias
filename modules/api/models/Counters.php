<?php

namespace app\modules\api\models;

use Yii;
use app\models\Api;
use app\models\Counters as C;
use app\models\Abonents;
use app\models\AddrHouses;

class Counters {

    static public $errors = [];

    static public function getClientCounters() {
        $data = [];
        foreach (C::find()->where(['client_id' => Api::$client->id])->asArray()->all() as $row) {
            $data[$row['id']] = [
                'id' => $row['id'],
                'number' => $row['number'],
                'model' => $row['model'],
                'begin' => $row['begin'],
                'date' => $row['date'],
                'service_id' => $row['service_id'],
                'house_id' => $row['house_id'],
                'flat_id' => $row['flat_id'],
                'editable' => $row['device_id'] === null,
            ];
        }
        return $data;
    }

    static public function saveClientCounter() {
        $keys = array_diff(['id', 'number', 'model', 'begin', 'date', 'service_id', 'house_id', 'flat_id'], array_keys(Api::$data));
        if (count($keys) > 0) {
            self::$errors[] = 'відсутні наступні дані: ' . implode(', ', $keys);
        }
        Yii::$app->controller->logFname = 'counter' . Api::$data['id'];
        if (Api::$data['id'] > 0) {
            $counter = C::find()->where(['id' => Api::$data['id'], 'client_id' => Api::$client->id])->one();
            if ($counter === null) {
                self::$errors[] = 'некоректний ідентифікатор приладу: ' . Api::$data['id'];
                return;
            }
            if ($counter->device_id > 0) {
                self::$errors[] = "оновлення лічильника №{$counter->number} неможливо.  Зверниться з цим питанням до адміністратора системи.";
                return;
            }
        } else {
            $counter = new C();
        }
        $house = AddrHouses::findModel(Api::$data['house_id']);
        $counter->client_id = Api::$client->id;
        $counter->number = Api::$data['number'];
        $counter->model = Api::$data['model'];
        $counter->begin = Api::$data['begin'];
        $counter->date = Api::$data['date'];
        $counter->service_id = Api::$data['service_id'];
        $counter->city_id = $house->city_id;
        $counter->street_id = $house->street_id;
        $counter->house_id = $house->id;
        $counter->flat_id = Api::$data['flat_id'];
        $res = $counter->saveModel();
        if (!$res) {
            self::$errors = $counter->getErrorSummary();
        }
    }

    static public function deleteCounter() {
        $keys = array_diff(['id'], array_keys(Api::$data));
        if (count($keys) > 0) {
            self::$errors[] = 'відсутні наступні дані: ' . implode(', ', $keys);
        }
        Yii::$app->controller->logFname = 'counter' . Api::$data['id'];
        $counter = C::findOne(Api::$data['id']);
        if ($counter === null) {
            self::$errors[] = 'некоректний ідентифікатор будинку: ' . Api::$data['id'];
            return;
        }
        if (!$counter->isAllowDelete()) {
            self::$errors[] = 'видалення приладу неможливо';
            return;
        }
        $counter->delete();
    }

}
