<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_Offices extends BaseActiveRecord {

    const DEFAULT_REF = '1692283f-e1c2-11e3-8c4a-0050568002cf';
    const DEFAULT_NO = '20';

    /**
     * @return string the associated database table name
     */
    static public function tableName() {
        return 'np_offices';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'Ref' => 'Идентификатор адреса',
            'SiteKey' => 'Код отделения',
            'Description' => 'Название отделения на украинском',
            'DescriptionRu' => 'Название отделения на русском',
            'Phone' => 'Телефон',
            'TypeOfWarehouse' => 'Тип отделения',
            'Number' => 'Номер отделения',
            'CityRef' => 'Идентификатор населенного пункта',
            'CityDescription' => 'Название населенного пункта на украинском',
            'CityDescriptionRu' => 'Название населенного пункта на русском',
            'Longitude' => 'Широта',
            'Latitude' => 'Долгота',
            'PostFinance' => 'Наличие кассы Пост-Финанс',
            'BicycleParking' => 'BicycleParking',
            'POSTerminal' => 'Наличие пос-терминала на отделении',
            'InternationalShipping' => 'Возможность оформления Международного отправления',
            'TotalMaxWeightAllowed' => 'Максимальный вес отправления',
            'PlaceMaxWeightAllowed' => 'Максимальный вес одного места отправления',
        );
    }

//    static public function keyval($city_id) {
//        $cmd = Yii::$app->db->getMasterPdo()->prepare('SELECT Ref, DescriptionRu FROM ' . self::tableName() . ' WHERE _city_id=:city_id ORDER BY Number');
//        $cmd->execute(array(':city_id' => $city_id));
//        return $cmd->fetchAll(\PDO::FETCH_KEY_PAIR);
//    }

    static public function keyval($city_ref) {
        $cmd = Yii::$app->db->getMasterPdo()->prepare('SELECT Number, Description FROM ' . self::tableName() . ' WHERE CityRef=:city_ref ORDER BY Number');
        $cmd->execute(array(':city_ref' => $city_ref));
        return $cmd->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function getName($ciry_ref, $num) {
        return self::find()->select('DescriptionRu')->where(['CityRef' => $ciry_ref, 'Number' => $num])->scalar();
    }

    static public function getRef($num) {
        return self::find()->select('Ref')->where(['CityRef' => NP_CitiesNp::DEFAULT_REF, 'Number' => $num])->scalar();
    }

}
