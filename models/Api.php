<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\components\Misc;

class Api {

    const TEST_PKEY = 'bCUwKyKcmJgYwuaVbbxlZgW5xj4cOBhk';

    static public $input;
    static public $data;
    static public $message = '';

    static public function errorsToString($model) {
        $message = '';
        foreach ($model->getErrors() as $errors) {
            $message .= implode("\n", $errors) . "\n";
        }
        return $message;
    }

    static public function getTestHash() {
        return self::getHash(self::TEST_PKEY, Api::$data['dataset'], Api::$data['time']);
    }

    static public function getHash($pkey, $data, $time) {
        return hash('sha256', "{$pkey}{$data}{$time}");
    }

    static public function checkTime($time, $test = false) {
        if ($test === false && Yii::$app->user->getId() > 0) {
            return true;
        }
        return strtotime($time) >= strtotime('-5 minute');
    }

    static public function signRequest($data, $test = false) {
        $time = date('Y-m-d H:i:s');
        $data = json_encode($data);
        return array(
            'dataset' => $data,
            'time' => $time,
            'signature' => self::getHash($test ? self::TEST_PKEY : Settings::get(Settings::API_KEY), $data, $time),
        );
    }

    static public function signResponce($res, $data) {
        $time = date('Y-m-d H:i:s');
        return $dataset = json_encode($data);
//        return array(
//            'res' => $res,
//            'dataset' => $dataset,
//            'time' => $time,
//            'signature' => self::getHash(self::PKEY, $dataset, $time),
//        );
    }

    static public function processData($data) {
        if (!self::checkTime($data['time'])) {
            return false;
        }
        if (self::getHash(Settings::get(Settings::API_KEY), $data['dataset'], $data['time']) !== $data['signature']) {
            return false;
        }
        self::$data = json_decode($data['dataset'], true);
        return true;
    }

    static public function createTestUrl($route, $data = null) {
        if ($data === null) {
            $data = mt_rand(1000000, mt_getrandmax());
        }
        $params = self::signRequest($data, true);
        array_unshift($params, $route);
        return Url::toRoute($params);
    }

    static public function createDemoUrl($route, $data = null) {
        if ($data === null) {
            $data = mt_rand(1000000, mt_getrandmax());
        }
        $params = self::signRequest($data);
        array_unshift($params, $route);
        return Url::toRoute($params);
    }

    static public function request($url, $data, $method) {
        $ch = curl_init($url);
        $params = json_encode(self::signRequest($data));
        $headers = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . mb_strlen($params, 'utf-8'),
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, Yii::$app->name);
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
                'message' => 'API (' . $url . '): responce code ' . $status['http_code'],
                'responce' => $responce,
            );
        }
        return json_decode($responce, true);
    }

    static protected function request_file($fp, $url) {
        $ch = curl_init($url);
        $headers = array(
            'Accept: */*',
            'Accept-Encoding: deflate, br',
            'Connection: close',
        );
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        if (curl_errno($ch)) {
            Yii::dumpAlt(curl_errno($ch), 'download_1S');
            Yii::dumpAlt(curl_getinfo($ch), 'download_1S');
            return false;
        }
        $res = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200;
        curl_close($ch);
        return $res;
    }

    static public function download($path, $fname, $data) {
        try {
            $fp = fopen($fname, 'w+');
            if (!$fp) {
                Yii::dumpAlt('Create file error: ' . $fname, 'download_1S');
                return false;
            }
            $url = Settings::get(Settings::API_URL) . $path . '?' . http_build_query(self::signRequest($data));
            $res = self::request_file($fp, $url);
            fclose($fp);
            if ($res) {
                $data = json_decode(file_get_contents($fname), true);
                if (isset($data['message'])) {
                    self::$message = $data['message'];
                    Yii::dumpAlt(self::$message, 'download_1S');
                    return false;
                } else {
                    Misc::sendFile($fname, basename($fname));
                    return true;
                }
            }
            if (is_file($fname)) {
                Yii::dumpAlt(file_get_contents($fname), 'download_1S');
                unlink($fname);
            }
            return false;
        } catch (\Throwable $ex) {
            self::$message = Misc::internalErrorMessage();
            Yii::dumpAlt($ex->getMessage(), 'download_1S');
            return false;
        }
    }

}
