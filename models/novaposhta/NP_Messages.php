<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_Messages extends BaseActiveRecord {

    static public function tableName() {
        return 'np_messages';
    }

    static public function keyval() {
        $data = array();
        foreach (self::find()->asArray()->all() as $row) {
            $data[$row['MessageCode']] = $row['MessageDescriptionUA'] <> '' ? $row['MessageDescriptionUA'] : $row['MessageText'];
        }
        return $data;
    }

}
