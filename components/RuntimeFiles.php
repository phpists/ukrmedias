<?php

namespace app\components;

use \Yii;

class RuntimeFiles {

    static protected $file = array();
    static protected $fileUid = array();

    static public function getUid($group, $suffix = null) {
        if (isset(self::$fileUid["{$group}{$suffix}"])) {
            return self::$fileUid["{$group}{$suffix}"];
        }
        $uid = Yii::$app->user->getId();
        self::$fileUid["{$group}{$suffix}"] = preg_replace('/[^a-zа-я0-9_\-\/\.]/ui', '_', Yii::$app->getRuntimePath() . "/{$group}/{$uid}/{$suffix}");
        self::dir(self::$fileUid["{$group}{$suffix}"]);
        return self::$fileUid["{$group}{$suffix}"];
    }

    static public function get($group, $suffix = null) {
        if (isset(self::$file["{$group}{$suffix}"])) {
            return self::$file["{$group}{$suffix}"];
        }
        self::$file["{$group}{$suffix}"] = preg_replace('/[^a-zа-я0-9_\-\/\.]/ui', '_', Yii::$app->getRuntimePath() . "/{$group}/{$suffix}");
        self::dir(self::$file["{$group}{$suffix}"]);
        return self::$file["{$group}{$suffix}"];
    }

    static protected function dir($file) {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

}
