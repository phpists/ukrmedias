<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_CitiesNp extends BaseActiveRecord {

    const DEFAULT_REF = '8d5a980d-391c-11dd-90d9-001a92567626'; //CityRef of NP_Offices

    /**
     * @return string the associated database table name
     */
    static public function tableName() {
        return 'np_cities_np';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
        );
    }

    static public function keyval($areaRef) {
        $query = Yii::$app->db->getMasterPdo()->prepare('SELECT Ref, Description FROM ' . self::tableName() . ' WHERE Area=:area ORDER BY Description');
        $query->execute([':area' => $areaRef]);
        return $query->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function keyvalAc($areaRef, $q) {
        $query = Yii::$app->db->getMasterPdo()->prepare('SELECT Ref AS id, Description AS text FROM ' . self::tableName() . ' WHERE Area=:area AND Description LIKE :q ORDER BY Description');
        $query->execute([':area' => $areaRef, ':q' => "{$q}%"]);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    static public function keyval_alt() {
        return Yii::$app->db->getMasterPdo()->query('SELECT Ref, Description FROM np_cities_np ORDER BY FIELD(Description,"Київ") DESC, Description')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function getName($ref) {
        return self::find()->select('DescriptionRu')->where(['Ref' => $ref])->scalar();
    }

}
