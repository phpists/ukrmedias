<?php

/**
 * Yii bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
require __DIR__ . '/../vendor/autoload.php';
require (__DIR__ . '/../vendor/yiisoft/yii2/BaseYii.php');

/**
 * Yii is a helper class serving common framework functionalities.
 *
 * It extends from [[\yii\BaseYii]] which provides the actual implementation.
 * By writing your own Yii class, you can customize some functionalities of [[\yii\BaseYii]].
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Yii extends \yii\BaseYii {

    static function dump() {
        $args = func_get_args();
        $file = self::$app->getRuntimePath() . '/logs/app.log';
        $h = fopen($file, 'a+');
        while ($args) {
            $data = array_shift($args);
            if ($data instanceof \yii\db\Query) {
                $sql = preg_replace('/(WHERE|ORDER|LEFT|RIGHT|FROM|AND)/i', "\n$1", (clone $data)->createCommand()->getRawSql());
                fwrite($h, date('[Y-m-d H:i:s] ') . "{$sql}\n");
            } elseif ($data === PHP_EOL) {
                fwrite($h, "{$data}{$data}");
            } else {
                fwrite($h, date('[Y-m-d H:i:s] ') . var_export($data, true) . PHP_EOL);
            }
        }
        fclose($h);
    }

    static function dumpAlt($data, $fname = 'alt', $append = true) {
        $file = self::$app->getRuntimePath() . "/logs/{$fname}.log";
        if (is_string($data)) {
            file_put_contents($file, date('[Y-m-d H:i:s] ') . $data . PHP_EOL, $append ? FILE_APPEND : null);
        } else {
            file_put_contents($file, date('[Y-m-d H:i:s] ') . var_export($data, true) . PHP_EOL, $append ? FILE_APPEND : null);
        }
    }

    static public function transaction($func) {
        $t = Yii::$app->db->beginTransaction();
        $res = $func();
        $res ? $t->commit() : $t->rollback();
        return $res;
    }

}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require(__DIR__ . '/../vendor/yiisoft/yii2/classes.php');
Yii::$container = new yii\di\Container();
