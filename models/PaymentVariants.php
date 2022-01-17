<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class PaymentVariants extends \app\components\BaseActiveRecord {

    const BANK = 1;

    public static function tableName() {
        return 'payment_variants';
    }

    public function rules() {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels() {
        return [
            'title' => 'Найменування',
        ];
    }

    public function search() {
        $query = self::find()->orderBy(['title' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        return $dataProvider;
    }

    public function saveModel() {
        $res = $this->save();
        return $res;
    }

    public function getTitle() {
        return $this->title;
    }

    public function isAllowDelete() {
        return $this->id <> self::BANK;
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

    static public function keyval() {
        return Yii::$app->db->getMasterPdo()->query('SELECT DISTINCT id,title FROM ' . self::tableName() . ' ORDER BY title')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}
