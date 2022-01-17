<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\Misc;

class PreOrdersDetails extends \app\components\BaseActiveRecord {

    public static function tableName() {
        return 'preorders_details';
    }

    public function rules() {
        return [
        ];
    }

    public function attributeLabels() {
        return [
            'brand' => 'Торгова марка',
            'title' => 'Найменування',
            'article' => 'Артикул',
            'code' => 'Код',
            'qty' => 'Кількість',
            'price' => 'Ціна за од.',
            'size' => 'Розмір',
            'color' => 'Колір',
            'weight' => 'Вага, кг',
            'volume' => 'Об`єм, куб.м',
        ];
    }

    static public function search($doc_id) {
        $query = self::find()->where(['doc_id' => $doc_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        return $dataProvider;
    }

    static public function findList($doc_id) {
        return self::find()->where(['doc_id' => $doc_id])->all();
    }

    public function saveModel($doc_id, $qty, $goods, $variant) {
        $this->setAttributes($goods->getAttributes(['brand', 'title', 'article', 'code']), false);
        $this->setAttributes($variant->getAttributes(['size', 'color']), false);
        $this->doc_id = $doc_id;
        $this->goods_id = $goods->id;
        $this->variant_id = $variant->id;
        $this->bar_code = (string) $variant->barCode;
        $this->qty = $qty;
        $this->base_price = $goods->getBasePrice();
        $this->price = $goods->getPrice();
        $this->discount = $goods->getBasePrice() - $goods->getPrice();
        $this->weight = $goods->weight;
        $this->volume = $goods->volume;
        $res = $this->save();
        return $res;
    }

    public function getDate() {
        return date('d.m.Y', strtotime($this->date));
    }

    public function getPrice() {
        return $this->price;
    }

    public function getAmount() {
        return Misc::round($this->qty * $this->price);
    }

    public function defineDiscount() {
        return Misc::round(100 * $this->discount / $this->base_price);
    }

    public function getAdminTitle($separator = '<br/>') {
        $data = $this->title;
        if ($this->size <> '' || $this->color <> '') {
            $data .= $separator;
        }
        $data .= $this->size;
        if ($this->size <> '' && $this->color <> '') {
            $data .= ' ';
        }
        $data .= $this->color;
        return $data;
    }

    public function getAdminCode($separator = '<br/>') {
        return "{$this->article}{$separator}{$this->code}";
    }

    static public function calcOrderSummary($id) {
        return Yii::$app->db->createCommand('SELECT SUM(qty*price) AS amount, SUM(qty*discount) AS discount, SUM(qty*weight) AS weight, SUM(qty*volume) AS volume FROM ' . self::tableName() . ' WHERE doc_id=:id', [':id' => $id])->queryOne();
    }

    static public function getGoodsQty($id) {
        return Yii::$app->db->createCommand('SELECT COUNT(*) FROM ' . self::tableName() . ' WHERE doc_id=:id', [':id' => $id])->queryScalar();
    }

    public function getGoods() {
        if ($this->goods_id == '') {
            return new Goods();
        }
        $key = "_goods_{$this->goods_id}";
        $value = Yii::$app->cache->get($key);
        if (!$value) {
            $value = $this->hasOne(Goods::className(), ['id' => 'goods_id'])->one();
            Yii::$app->cache->set($key, $value);
        }
        return $value;
    }

    public function getVariant() {
        if ($this->variant_id == '') {
            $value = new Variants();
        } else {
            $key = "_variant_{$this->variant_id}";
            $value = Yii::$app->cache->get($key);
            if (!$value) {
                $value = $this->hasOne(Variants::className(), ['id' => 'variant_id'])->one();
                Yii::$app->cache->set($key, $value);
            }
        }
        $value->initGoodsData($this->goods_id);
        return $value;
    }

}
