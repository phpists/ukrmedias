<?php

namespace app\models;

use Yii;

class Export {

    static public function preorders() {
        foreach (PreOrders::find()->where(['to_1s' => 0, 'status_id' => PreOrders::STATUS_NEW])->all() as $model) {
            $data = $model->getAttributes(['id', 'date', 'amount', 'discount', 'delivery_id', 'payment_title', 'client_note', 'price_type_id',]);
            $data['firm_id'] = Firms::findOne($model->firm_id)->id_1s;
            if ($data['firm_id'] == '') {
                continue;
            }
            $data['address'] = $model->getAddressTxt();
            $data['details'] = [];
            foreach ($model->getDetails() as $details) {
                array_push($data['details'], [
                    'id' => $details->goods_id,
                    'variant_id' => $details->variant_id,
                    'qty' => $details->qty,
                    'price' => $details->base_price,
                    'discount' => $details->discount,
                ]);
            }
            self::send('/save_order', $data, function () use ($model) {
                $model->to_1s = 1;
                $model->update(false, ['to_1s']);
            });
        }
    }

    static public function firms() {
        foreach (Firms::find()->where(['to_1s' => 0])->all() as $model) {
            $phones = $model->getPhones();
            $data = [
                'site_id' => $model->id,
                'title' => $model->title,
                'phone' => $phones,
                'id_diler' => $model->id_1s,
            ];
            self::send('/save_firm', $data, function ($resp) use ($model) {
                if ($resp['id'] <> '') {
                    $model->to_1s = 1;
                    $model->id_1s = $resp['id'];
                    $model->update(false, ['to_1s', 'id_1s']);
                }
            });
        }
    }

    static protected function send($url, $data, $func) {
        try {
            $resp = Api::request(Settings::get(Settings::API_URL) . $url, $data, 'POST');
            if (isset($resp['res']) && $resp['res'] === true) {
                $func($resp);
            } else {
                Yii::dumpAlt($url);
                Yii::dumpAlt($data);
                Yii::dumpAlt($resp);
            }
        } catch (Exception $e) {
            Yii::dump($url, $data, $e->getMessage());
        }
        sleep(2);
    }

}
