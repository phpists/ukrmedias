<?php

namespace app\models\novaposhta;

use app\components\BaseActiveRecord;
use Yii;

class NP_Areas extends BaseActiveRecord {

    const DEFAULT_REF = '71508131-9b87-11de-822f-000c2965ae0e';

    /**
     * @return string the associated database table name
     */
    static public function tableName() {
        return 'np_areas';
    }

    static public function keyval() {
        return Yii::$app->db->getMasterPdo()->query('SELECT Ref, Description FROM np_areas ORDER BY FIELD(Description,"Київська") DESC, Description')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}
