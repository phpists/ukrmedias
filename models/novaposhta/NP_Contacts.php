<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_Contacts extends BaseActiveRecord {

    const DEFAULT_REF = '6db5b9ca-412e-11eb-8513-b88303659df5';

    /**
     * @return string the associated database table name
     */
    static public function tableName() {
        return 'np_partners_contacts';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'Ref' => 'Идентификатор контактного лица',
            'Description' => 'Контактное лицо',
            'Phones' => 'Телефон',
            'Email' => 'Email',
            'LastName' => 'Фамилия',
            'FirstName' => 'Имя',
            'MiddleName' => 'Отчество',
        );
    }

    static public function keyval($ref) {
        $cmd = Yii::$app->db->getMasterPdo()->prepare('SELECT Ref, Description FROM ' . self::tableName() . ' WHERE _partner_ref=:ref ORDER BY Description');
        $cmd->execute(array(':ref' => $ref));
        return $cmd->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function findDefault() {
        return self::findOne(self::DEFAULT_REF);
    }

}
