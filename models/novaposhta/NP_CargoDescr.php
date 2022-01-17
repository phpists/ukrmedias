<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_CargoDescr extends BaseActiveRecord {

    const DEFAULT_REF = '2fe893e7-33ee-11e3-b441-0050568002cf';
    const DEFAULT_DESCR = 'Одяг';

    /**
     * @return string the associated database table name
     */
    static public function tableName() {
        return 'np_cargo_descr';
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

    static public function keyval_alt() {
        return Yii::$app->db->getMasterPdo()->query('SELECT Description, Description FROM ' . self::tableName() . ' ORDER BY Description')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}
