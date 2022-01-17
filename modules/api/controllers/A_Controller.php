<?php

namespace app\modules\api\controllers;

use Yii;
use app\models\Api;
use app\components\RuntimeFiles;

class A_Controller extends \yii\web\Controller {

    public $logName;
    protected $errors = [];

    public function beforeAction($action) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($action->id === 'error') {
            return true;
        }
        if (Yii::$app->request->isGet) {
            $data = (array) Yii::$app->request->get();
        } else {
            $data = (array) json_decode(Yii::$app->request->getRawBody(), true);
        }
        if (!isset($data['time']) || !isset($data['dataset']) || !isset($data['signature'])) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют обязательные поля json-данных.']);
        }
        if (in_array($action->id, ['test'])) {
            Api::$data = $data;
            return true;
        }
        if (!Api::processData($data)) {
            $this->responce(['res' => false, 'message' => 'Некорректная подпись или метка времени.']);
        }
        $method = Yii::$app->request->method;
        file_put_contents(RuntimeFiles::get('api_data', preg_replace('/[^a-z]/i', '_', $this->route) . "_{$method}.php"), var_export(Api::$data, true));
        return true;
    }

    public function afterAction($action, $result) {
        if ($this->logName !== null) {
            if (isset(Api::$data['photos'])) {
                Api::$data['photos'] = [];
            }
            $this->toLog();
        }
        return parent::afterAction($action, $result);
    }

    public function toLog() {
        file_put_contents(RuntimeFiles::get("api_data/{$this->route}", "{$this->logName}.php"), '<?php return ' . var_export(Api::$data, true) . PHP_EOL . ';/*' . PHP_EOL . Api::$message . PHP_EOL . '*/');
    }

    public function responce($data) {
        $json = json_encode($data);
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Length:' . mb_strlen($json, 'utf-8'));
        echo $json;
        exit(0);
    }

    protected function isArray() {
        $this->errors = [];
        foreach (func_get_args() as $attr) {
            if (!is_array(Api::$data[$attr])) {
                $this->errors[] = "Некорректный тип данных для поля {$attr}.";
            }
        }
        if (count($this->errors) > 0) {
            $this->responce(['res' => false, 'message' => implode(' ', $this->errors)]);
        }
    }

    protected function isValidArrayAlt($data, $fields) {
        $this->isArray($data);
        if (count($data) > 0) {
            $keys = array_diff($fields, array_keys(current($data)));
            if (count($keys) > 0) {
                $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
            }
        }
    }

    protected function isValidArray($data) {
        $this->errors = [];
        foreach ($data as $attr => $fields) {
            if (count(Api::$data[$attr]) > 0) {
                $keys = array_diff($fields, array_keys(current(Api::$data[$attr])));
                if (count($keys) > 0) {
                    $this->errors[] = "Отсутствуют следующие данные для поля {$attr}: " . implode(', ', $keys) . '.';
                }
            }
        }
        if (count($this->errors) > 0) {
            $this->responce(['res' => false, 'message' => implode(' ', $this->errors)]);
        }
    }

}
