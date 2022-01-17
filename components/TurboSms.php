<?php

namespace app\components;

use app\models\Settings;
use Yii;

class TurboSms {

    const BASE_URL = 'https://api.turbosms.ua';

    static public function request($path, $data) {
        try {
            $ch = curl_init(self::BASE_URL . $path);
            $params = json_encode($data);
            $headers = array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:' . mb_strlen($params, 'utf-8'),
                'Authorization: Basic ' . Settings::get(Settings::SMS_TOKEN)
            );
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Levsha site');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $responce = curl_exec($ch);
            $status = curl_getinfo($ch);
            curl_close($ch);
            if ($status['http_code'] !== 200) {
                return array(
                    'res' => false,
                    'message' => 'API (' . self::BASE_URL . $path . '): responce code ' . $status['http_code'],
                    'data' => $responce,
                );
            }
            $data = json_decode($responce, true);
            if (!is_array($data)) {
                $dir = Yii::$app->getRuntimePath() . '/sms_data';
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
                file_put_contents($dir . '/' . preg_replace('/[^a-z0-9]/i', '_', $path) . '-' . mt_rand(1, 3) . '.json', $responce);
                $data = array('res' => false, 'message' => 'API (' . self::BASE_URL . $path . '): not JSON-responce', 'data' => $responce);
            }
            return array('res' => true, 'message' => '', 'data' => $data);
        } catch (Throwable $ex) {
            Yii::dump(__METHOD__, $path, $data);
            Yii::dump($ex->getMessage());
            return array('res' => false, 'message' => $ex->getMessage());
        }
    }

    static public function toLog($info, $details) {
        Yii::$app->db->createCommand('INSERT INTO turbo_sms (info,details) VALUES(:info,:details)', [':info' => $info, ':details' => $details])->execute();
    }

    static public function send($numbers, $text) {
        if (!is_array($numbers)) {
            $numbers = [$numbers];
        }
        self::normalize($numbers);
        $numbers = array_filter(array_unique($numbers));
        $offset = 0;
        $limit = 5;
        while (($set = array_slice($numbers, $offset, $limit))) {
            $data = self::request('/message/send.json', [
                        'sender' => Settings::get(Settings::SMS_SENDER),
                        'recipients' => $set,
                        'sms' => [
                            'text' => $text,
                        ],
            ]);
            self::toLog(json_encode($set), $text);
            self::toLog('send', json_encode($data));
            $offset += $limit;
        }
    }

    static protected function normalize(&$numbers) {
        foreach ($numbers as $i => $phone) {
            $phone = preg_replace('/[^\d]+/', '', $phone);
            if (strpos($phone, '0') === 0) {
                $phone = "38{$phone}";
            }
            if (strlen($phone) >= 12) {
                $numbers[$i] = $phone;
                continue;
            }
            if (strlen($phone) > 0) {
                self::toLog(__METHOD__, " - number={$phone}?");
            }
            unset($numbers[$i]);
        }
    }

    static public function clean() {
        Yii::$app->db->createCommand('DELETE FROM turbo_sms WHERE date<CURDATE()-INTERVAL 1 MONTH')->execute();
    }

}
