<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\Misc;

class OrdersDetails extends PreOrdersDetails {

    public $weight;
    public $volume;

    public static function tableName() {
        return 'orders_details';
    }

    public function saveModel($doc_id, $attrs, $goods, $variant) {
        $this->setAttributes($goods->getAttributes(['brand', 'title', 'article', 'code']), false);
        $this->setAttributes($variant->getAttributes(['size', 'color']), false);
        $this->doc_id = $doc_id;
        $this->goods_id = $goods->id;
        $this->variant_id = $variant->id;
        $this->qty = (int) $attrs['qty'];
        $this->bar_code = $attrs['bar_code'];
        $this->base_price = Misc::priceFilter($attrs['price']);
        $this->discount = Misc::priceFilter($attrs['discount']);
        $this->price = $this->base_price * (1 - $this->discount / 100);
        $this->weight = (float) $goods->weight * (int) $attrs['qty'];
        $this->volume = (float) $goods->volume * (int) $attrs['qty'];
        $res = $this->save(false);
        return $res;
    }

    static public function calcOrderSummary($id) {
        $data = Yii::$app->db->createCommand('SELECT SUM(qty*price) AS amount, SUM(qty*discount) AS discount FROM ' . self::tableName() . ' WHERE doc_id=:id', [':id' => $id])->queryOne();
        return $data;
    }

//    public function rules() {
//        return [
//        ];
//    }
//
//    public function attributeLabels() {
//        return [
//            'title' => 'Найменування',
//            'article' => 'Артикул',
//            'code' => 'Код',
//            'qty' => 'Кількість',
//            'price' => 'Ціна за од.',
//            'size' => 'Розмір',
//            'color' => 'Колір',
//        ];
//    }
//
//    static public function search($doc_id) {
//        $query = self::find()->where(['doc_id' => $doc_id]);
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'pagination' => false,
//            'sort' => false,
//        ]);
//        return $dataProvider;
//    }
//
//    static public function findList($doc_id) {
//        return self::find()->where(['doc_id' => $doc_id])->all();
//    }
//
//    public function saveModel($doc_id, $qty, $goods, $variant) {
//        $this->setAttributes($goods->getAttributes(['brand', 'title', 'article', 'code', 'bar_code']), false);
//        $this->setAttributes($variant->getAttributes(['size', 'color']), false);
//        $this->doc_id = $doc_id;
//        $this->goods_id = $goods->id;
//        $this->variant_id = $variant->id;
//        $this->qty = $qty;
//        $this->base_price = $goods->getBasePrice();
//        $this->price = $goods->getPrice();
//        $this->discount = $goods->getBasePrice() - $goods->getPrice();
//        $res = $this->save();
//        return $res;
//    }
//
//    public function getDate() {
//        return date('d.m.Y', strtotime($this->date));
//    }
//
//    public function getPrice() {
//        return $this->price;
//    }
//
//    public function getAmount() {
//        return Misc::round($this->qty * $this->price);
//    }
//
//    public function defineDiscount() {
//        return Misc::round(100 * $this->discount / $this->base_price);
//    }
//
//    public function getAdminTitle() {
//        $data = $this->title;
//        if ($this->size <> '' || $this->color <> '') {
//            $data .= '<br/>';
//        }
//        $data .= $this->size;
//        if ($this->size <> '' && $this->color <> '') {
//            $data .= ' / ';
//        }
//        $data .= $this->color;
//        return $data;
//    }
//
//    public function getAdminCode() {
//        return "{$this->article}<br/>{$this->code}";
//    }
//
//    static public function calcOrderSummary($id) {
//        return Yii::$app->db->createCommand('SELECT SUM(qty*price) AS amount, SUM(qty*discount) AS discount FROM ' . self::tableName() . ' WHERE doc_id=:id', [':id' => $id])->queryOne();
//    }
}
