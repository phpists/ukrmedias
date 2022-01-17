<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_Senders extends BaseActiveRecord {
    const DEFAULT_REF = '074bec06-339f-11ea-9937-005056881c6b';

    /**
     * @return string the associated database table name
     */
    static public function tableName() {
        return 'np_partners';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'Ref' => 'Идентификатор',
            'Description' => 'Наименование',
            'City' => 'Идентификатор города',
            'FirstName' => '',
            'LastName' => '',
            'MiddleName' => '',
            'OwnershipFormRef' => 'Идентификатор формы собственности',
            'OwnershipFormDescription' => 'Фотма собственности',
            'EDRPOU' => 'ЕГРПОУ',
            'CounterpartyType' => 'Тип',
            'CityDescription' => 'Город'
        );
    }

    static public function keyval() {
        return Yii::$app->db->getMasterPdo()->query('SELECT Ref, Description FROM ' . self::tableName() . ' WHERE _type="Sender" ORDER BY Description')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}
