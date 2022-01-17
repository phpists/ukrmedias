<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_DeliveryTypes extends BaseActiveRecord {

    const DEFAULT_REF = 'WarehouseWarehouse';

    /**
     * @return string the associated database table name
     */
    static public function tableName() {
        return 'np_delivery_types';
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
