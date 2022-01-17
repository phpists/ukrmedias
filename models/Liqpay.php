<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

class Liqpay {

    public $responce;
    static private $_inst;
    private $publicKey;
    private $privateKey;
    private $serverUrl;

    private function __construct($client) {
        $this->serverUrl = Url::toRoute(['/api/payments/log', ['id' => $client->id]]);
        $this->publicKey = $client->liqpay_id;
        $this->privateKey = $client->liqpay_key;
    }

    private function __clone() {
        ;
    }

    /**
     *
     * @return Liqpay
     */
    static public function getInstance($client) {
        if (self::$_inst === null) {
            self::$_inst = new self($client);
        }
        return self::$_inst;
    }

    private function sign($data) {
        return sha1("{$this->privateKey}{$data}{$this->privateKey}", 1);
    }

    public function widget($params, $resultUrl = null) {

//        echo app\models\Liqpay::getInstance()->widget(array(
//            'action' => 'pay',
//            'amount' => '1',
//            'currency' => 'USD',
//            'description' => 'description text',
//            'order_id' => "{$from}_{$to}_{$flat_id}_{$service_id}",
//            'version' => '3'
//                ), 'qqq');
        #$params = ['version' => '3', 'action' => 'pay', 'amount' => '3', 'currency' => 'UAH', 'description' => 'test', 'order_id' => '000001', 'language' => 'uk', 'paytypes' => 'card', 'result_url' => $this->resultUrl, 'server_url' => $this->serverUrl];
        $params['public_key'] = $this->publicKey;
        $params['version'] = '3';
        $params['action'] = 'pay';
        $params['currency'] = 'UAH';
        $params['language'] = Yii::$app->language;
        $params['paytypes'] = 'card';
        if ($resultUrl) {
            $params['result_url'] = $resultUrl;
        }
        $params['server_url'] = $this->serverUrl;
        $data = base64_encode(json_encode($params));
        $signature = base64_encode($this->sign($data));
        return sprintf('<div id="liqpay_checkout"></div>
  <script>
    window.LiqPayCheckoutCallback = function() {
    LiqPayCheckout.init({data: "%s",signature: "%s",embedTo: "#liqpay_checkout",language: "%s",mode: "embed"})
        .on("liqpay.callback", function(data){})
        .on("liqpay.ready", function(data){})
        .on("liqpay.close", function(data){});
    };
  </script>
  <script src="//static.liqpay.ua/libjs/checkout.js" async></script>', $data, $signature, Yii::$app->language);
    }

    public function isValid($data, $signature) {
        $responceData = base64_decode($data);
        $sign = base64_decode($signature);
        if ($sign === $this->sign($responceData)) {
            $this->responce = json_decode($responceData);
            return true;
        } else {
            return false;
        }
    }

    public function toLog() {
        Yii::$app->db->createCommand()->insert('payments_log', ['data' => $this->responce])->execute();
        if ($this->responce->status === 'success') {
            return Yii::$app->db->lastInsertID;
        } else {
            return false;
        }
    }

}
