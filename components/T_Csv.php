<?php

namespace app\components;

use \Yii;
use app\models\AddrRegions;
use app\models\AddrAreas;
use app\models\AddrCities;
use app\models\AddrStreets;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\Tasks;
use app\models\Users;

trait T_Csv {

    static public $ok = 0;
    static public $errors = 0;
    static public $logFile;
    static public $info = [];

    static public function import($data, $taskRow, $delete = true) {
        self::$logFile = Yii::$app->getRuntimePath() . uniqid('/logs' . (new \ReflectionClass(get_called_class()))->getShortName() . '.log'); //$data['logFile'];
        $fname = Yii::$app->getRuntimePath() . uniqid('/');
        file_put_contents($fname, $data['csv']);
        $rowsQty = substr_count($data['csv'], PHP_EOL);
        $data['csv'] = null;
        $h = fopen($fname, 'r');
        $no = 0;
        while (($row = fgetcsv($h, 1000, ';', '"')) !== false) {
            $no++;
            if ($no === 1) {
                continue;
            }
            Tasks::setProgress($taskRow['id'], 100 * $no / $rowsQty);
            if (count(array_filter($row)) === 0) {
                continue;
            }
            if (!self::chk($row, count(self::$fields), $no)) {
                continue;
            }
            $t = Yii::$app->db->beginTransaction();
            try {
                $res = self::save(self::getAttributes(self::$fields, $row), $no, $taskRow['user_id']);
            } catch (\Throwable $ex) {
                Yii::dump($ex->getMessage());
                self::toLog('Строка ' . $no . ': ' . Misc::internalErrorMessage());
                $res = false;
            }
            if ($res) {
                self::$ok++;
                $t->commit();
            } else {
                self::$errors++;
                $t->rollback();
            }
        }
        self::toLog(strtr(self::$messageTpl, ['{ok}' => self::$ok, '{errors}' => self::$errors]));
        Tasks::setProgress($taskRow['id'], 100);
        fclose($h);
        if ($delete) {
            unlink($fname);
        }
    }

    static public function checkFields($fname) {
        $h = fopen($fname, 'r');
        $row = fgetcsv($h, 1000, ';', '"');
        self::chk($row, count(self::$fields));
        $row = array_filter((array) fgetcsv($h, 1000, ';'));
        if (!$row) {
            self::$info[] = "В файлі після заголовку немає строк з даними.";
            self::$errors++;
        }
        fclose($h);
        return self::$errors === 0;
    }

    static protected function chk(&$row, $qty, $no = null) {
        $filedsQty = count($row);
        if ($filedsQty !== $qty) {
            self::$errors++;
            if ($no > 0) {
                self::toLog("Строка {$no}: некоректна структура даних. Строка має {$filedsQty} полів. Очікується {$qty} полів, розделених символом <kbd>;</kbd>.");
            } else {
                self::$info[] = "Файл має некоректну структуру. Перша строка має {$filedsQty} полів. Очікується {$qty} полів, розделених символом <kbd>;</kbd>.";
            }
            return false;
        }
        return true;
    }

    static protected function getAttributes(&$fields, &$values) {
        $a = array_combine(array_keys($fields), $values);
        foreach ($a as $k => $v) {
            $a[$k] = trim($v);
        }
        return $a;
    }

    static protected function findCity($no, &$a) {
        if ($a['region'] === '') {
            $region_id = null;
        } else {
            $region_id = AddrRegions::findModel(['title' => $a['region']])->id;
        }
        if ($a['area'] === '') {
            $area_id = null;
        } else {
            $area_id = AddrAreas::findModel(['title' => $a['area']])->id;
        }
        $model = AddrCities::find()->where(['region_id' => $region_id, 'area_id' => $area_id, 'title' => $a['city']])->one();
        if ($model instanceof AddrCities) {
            return $model;
        }
        if (empty($region_id)) {
            self::toLog('Строка ' . $no . ': обласний центр "' . $a['city'] . '" відсутній в базі даних адрес. Зверниться з цим питанням до адміністратора системи.');
        } elseif (empty($area_id)) {
            self::toLog('Строка ' . $no . ': область "' . $a['region'] . '" або районний центр "' . $a['city'] . '" відсутні в базі даних адрес. Зверниться з цим питанням до адміністратора системи.');
        } else {
            self::toLog('Строка ' . $no . ': область "' . $a['region'] . '" або район "' . $a['area'] . '" або населений пункт "' . $a['city'] . '" відсутні в базі даних адрес. Зверниться з цим питанням до адміністратора системи.');
        }
    }

    static protected function findStreet($no, &$a, $city) {
        $model = AddrStreets::find()->where(['city_id' => $city->id, 'title' => $a['street']])->one();
        if ($model instanceof AddrStreets) {
            return $model;
        }
        self::toLog('Строка ' . $no . ': вулиця "' . $a['street'] . '" для вказаного населеного пункту відсутня в базі даних адрес. Зверниться з цим питанням до адміністратора системи.');
    }

    static protected function findHouse($no, &$a, $street) {
        $model = AddrHouses::find()->where(['street_id' => $street->id, 'post_index' => $a['post_index'], 'no' => $a['house']])->one();
        if ($model instanceof AddrHouses) {
            return $model;
        }
        self::toLog('Строка ' . $no . ': будинок "' . $a['house'] . '" на вулиці "' . $a['street'] . '" з поштовим індексом "' . $a['post_index'] . '" відсутній в базі даних адрес. Зверниться з цим питанням до адміністратора системи.');
    }

    static protected function findFlat($no, &$a, $house) {
        return AddrFlats::findModel(['house_id' => $house->id, 'no' => $a['flat']]);
//        if ($model instanceof AddrFlats) {
//            return $model;
//        }
//        self::toLog('Строка ' . $no . ': приміщення "' . $a['flat'] . '" в будинку "' . $a['house'] . '" на вулиці "' . $a['street'] . '" з поштовим індексом "' . $a['post_index'] . '" відсутня базі даних адрес. Необхідно попередньо додати абонента.');
    }

    static protected function findUser(&$a) {
        $user = Users::find()->where(['email' => $a['email']])->one();
        if ($user === null) {
            $user = Users::find()->where(['phone' => $a['phone']])->one();
        }
        return $user === null ? new Users() : $user;
    }

    static public function create($file, $data) {
        $rows = [];
        $rows[] = '"' . implode('";"', self::$fields) . '"';
        foreach ($data as $model) {
            $rows[] = '"' . implode('";"', self::getRowData($model)) . '"';
        }
        file_put_contents($file, pack('H*', 'EFBBBF') . implode(PHP_EOL, $rows));
    }

    static public function getTmpFile() {
        return RuntimeFiles::get('import-csv', (new \ReflectionClass(get_called_class()))->getShortName());
    }

    static protected function getLogFile() {
        return (new \ReflectionClass(get_called_class()))->getShortName();
    }

    static protected function toLog($info) {
        Yii::$app->db->createCommand()->insert('import_log', ['model' => self::getLogFile(), 'info' => "<li>{$info}</li>"])->execute();
    }

    static public function getLogTime() {
        return (new \yii\db\Query)
                        ->select(new \yii\db\Expression('DATE_FORMAT(date,"%d.%m.%Y %H:%i")'))
                        ->from('import_log')
                        ->where(['model' => self::getLogFile()])
                        ->orderBy(['date' => SORT_DESC])
                        ->limit(1)
                        ->scalar();
    }

    static public function getLogInfo() {
        return (new \yii\db\Query)
                        ->select(new \yii\db\Expression('GROUP_CONCAT(info SEPARATOR "\n")'))
                        ->from('import_log')
                        ->where(['model' => self::getLogFile()])
                        ->groupBy('model')
                        ->scalar();
    }

    static public function initLog() {
        return Yii::$app->db->createCommand()->delete('import_log', ['model' => self::getLogFile()])->execute();
    }

}
