<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class DeliveryVariants extends \app\components\BaseActiveRecord {

    const UNDEFINED = 'undefined';
    const NOVA_POSHTA = '0a50ca53-c3a4-11e7-80cb-d8cb8adfdbf9';
    const SELF = '0a50ca54-c3a4-11e7-80cb-d8cb8adfdbf9';

    static public $kvClient;

    public static function tableName() {
        return 'delivery_variants';
    }

    public function rules() {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 45],
            [['is_allow'], 'boolean'],
            [['price', 'amount'], 'filter', 'filter' => ['\app\components\Misc', 'priceFilter']],
        ];
    }

    public function attributeLabels() {
        return [
            'title' => 'Найменування',
            'price' => 'Ціна доставки',
            'amount' => 'Мін. сума замовлення',
            'is_allow' => 'Дозволено для заявки',
        ];
    }

    public function search() {
        $query = self::find()->where(['<>', 'id', self::UNDEFINED])->orderBy(['title' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        return $dataProvider;
    }

    static public function import($attrs) {
        $model = self::findModel($attrs['id']);
        $model->setAttributes($attrs, false);
        $model->save();
        return $model;
    }

    public function updateModel() {
        $res = $this->save(true, ['price', 'amount', 'is_allow']);
        return $res;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

    static public function keyval() {
        return Yii::$app->db->getMasterPdo()->query('SELECT DISTINCT id,title FROM ' . self::tableName() . ' ORDER BY title')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function keyvalClient() {
        if (self::$kvClient === null) {
            self::$kvClient = Yii::$app->db->getMasterPdo()->query('SELECT DISTINCT id,title FROM ' . self::tableName() . ' WHERE is_allow=1 ORDER BY title')->fetchAll(\PDO::FETCH_KEY_PAIR);
        }
        return self::$kvClient;
    }

}
