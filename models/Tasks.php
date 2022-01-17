<?php

namespace app\models;

use \Yii;
use app\components\Auth;
use app\components\Misc;

class Tasks {

    const QTY = 100;
    const SET_PROMO_GOODS = 1;
    const REFRESH_CACHE = 2;
    const AFTER_UPDATE_STOCK = 3;

    static public $consoleCommand;
    static protected $tableName = 'tasks';
    static protected $user_id;

    /**
     * Returns the database connection component.
     * @return \yii\db\Connection the database connection.
     */
    static public function getDb() {
        return Yii::$app->db;
    }

    static public function start($task_id, $data = null, $delay = null) {
        $data = json_encode($data);
        $hash = md5($data);
        $user_id = self::defineUserId();
        $exists = self::getDb()->createCommand('SELECT 1 FROM ' . self::$tableName . ' WHERE task_id=:task_id  AND hash=:hash AND (user_id=:user_id OR user_id IS NULL) AND process_id IS NULL LIMIT 1',
                        [':task_id' => $task_id, ':hash' => $hash, ':user_id' => $user_id])->queryScalar();
        if ($exists) {
            return;
        }
        return self::getDb()->createCommand()->insert(self::$tableName, ['task_id' => $task_id, 'hash' => $hash, 'user_id' => $user_id, 'data' => $data, 'delay' => $delay])->execute();
    }

    static public function run() {
        $pid = Misc::uniqid();
        self::getDb()->createCommand('UPDATE ' . self::$tableName . ' SET process_id=:pid WHERE process_id IS NULL AND (delay IS NULL OR delay<=NOW()) LIMIT ' . self::QTY, [':pid' => $pid])->execute();
        foreach (self::getDb()->createCommand('SELECT * FROM ' . self::$tableName . ' WHERE process_id=:pid LIMIT ' . self::QTY, [':pid' => $pid])->queryAll() as $row) {
            $res = self::process($row);
            if ($res) {
                self::getDb()->createCommand('UPDATE ' . self::$tableName . ' SET process_id="ok" WHERE id=:id', [':id' => $row['id']])->execute();
            }
        }
    }

    static public function getResult($task_id, $data) {
        $data = json_encode($data);
        $hash = md5($data);
        $user_id = self::defineUserId();
        $res = self::getDb()->createCommand('SELECT result FROM ' . self::$tableName . ' WHERE task_id=:task_id AND hash=:hash AND (user_id=:user_id OR user_id IS NULL) AND process_id="ok" ORDER BY id DESC LIMIT 1',
                        [':task_id' => $task_id, ':hash' => $hash, ':user_id' => $user_id])->queryScalar();
        return strlen($res) > 0 ? base64_decode($res) : null;
    }

    static public function isRun($task_id) {
        return self::getDb()->createCommand('SELECT 1 FROM ' . self::$tableName . ' WHERE task_id=:task_id AND (user_id=:user_id OR user_id IS NULL) AND (process_id IS NULL OR process_id<>"ok") ORDER BY id DESC LIMIT 1',
                        [':task_id' => $task_id, ':user_id' => self::defineUserId()])->queryScalar();
    }

    static public function getProgress($task_id, $user_id = null) {
        return self::getDb()->createCommand('SELECT progress FROM ' . self::$tableName . ' WHERE task_id=:task_id AND (user_id=:user_id OR user_id IS NULL) ORDER BY id DESC LIMIT 1',
                        [':task_id' => $task_id, ':user_id' => self::defineUserId()])->queryScalar();
    }

    static public function setProgress($id, $value) {
        return self::getDb()->createCommand('UPDATE ' . self::$tableName . ' SET progress=:value WHERE id=:id', [':id' => $id, ':value' => $value])->execute();
    }

    static public function setResult($id, $value) {
        return self::getDb()->createCommand('UPDATE ' . self::$tableName . ' SET result=:value WHERE id=:id', [':id' => $id, ':value' => base64_encode($value)])->execute();
    }

    static protected function defineUserId() {
        if (PHP_SAPI === 'cli' || strpos(Yii::$app->controller->route, 'api') === 0) {
            return self::$user_id;
        }
        if (Auth::isClient()) {
            return Yii::$app->user->identity->client_id;
        }
        return Yii::$app->user->getId();
    }

    static protected function process($row) {
        self::$user_id = $row['user_id'];

        $data = json_decode($row['data'], true);
        switch ($row['task_id']):
            case self::SET_PROMO_GOODS:
                DataHelper::setPromoGoods(isset($data['id']) ? $data['id'] : null);
                $res = true;
                break;
            case self::REFRESH_CACHE:
                Misc::iterator(Category::find()->orderBy(['lft' => SORT_ASC]), function ($category) {
                    DataHelper::setCategoryData($category);
                });
                Misc::iterator(Promo::find(), function ($model) {
                    DataHelper::setPromoData($model->id);
                });
                DataHelper::setPromoData(null);
                $res = true;
                break;
            case self::AFTER_UPDATE_STOCK:
                Misc::iterator(Goods::find(), function ($model) {
                    DataHelper::updateGoodsData($model);
                });
                $res = true;
                break;
        endswitch;
        return $res;
    }

    static protected function transaction($func) {
        $transaction = self::getDb()->beginTransaction();
        $res = true;
        try {
            $func();
        } catch (\Throwable $ex) {
            $res = false;
            Yii::dump(__METHOD__ . $ex->getMessage());
        }
        $res ? $transaction->commit() : $transaction->rollback();
        return $res;
    }

    static public function autorun() {

    }

}
