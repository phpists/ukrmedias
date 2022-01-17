<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_PayerTypesBack extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    static public function tableName() {
        return 'np_payer_types_back';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'Ref' => 'Идентификатор',
            'Description' => 'Наименование',
        );
    }

    static public function keyval() {
        return Yii::$app->db->getMasterPdo()->query('SELECT Ref, Description FROM ' . self::tableName() . ' ORDER BY Description')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}
