<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class Variants extends \app\components\BaseActiveRecord {

    public $goodsModel;
    public $photoModel;
    public $barCode;
    public $qty;
    public $qty_alt;
    public $is_new = 0;

    public static function tableName() {
        return 'variants';
    }

    public function rules() {
        return [
            [['color', 'size'], 'string', 'max' => 45],
            [['color_code'], 'string', 'max' => 7],
        ];
    }

    public function attributeLabels() {
        return [
            'color' => 'Колір',
            'size' => 'Розмір',
            'qty' => 'Кількість на власному складі',
            'qty_alt' => 'Кількість на партнерському складі',
            'is_new' => 'Новинка',
        ];
    }

//    public function search() {
//        $query = self::find()->orderBy(['pos' => SORT_ASC, 'title' => SORT_ASC]);
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'pagination' => [
//                'pageSize' => DataHelper::PAGE_SIZE,
//            ],
//            'sort' => false,
//        ]);
//        if ($this->load(Yii::$app->request->get())) {
//            $query
//                    ->andFilterWhere(['like', 'title', $this->title])
//                    ->andFilterWhere(['=', 'type_id', $this->type_id]);
//        }
//        return $dataProvider;
//    }

    static public function import($goods, $attrs, $barCodes) {
        $dataModel = new self();
        $ids = [];
        foreach ($attrs as $row) {
            $ids[] = $row['variant_id'];
            $model = self::findModel(['id' => $row['variant_id']]);
            $model->setAttributes($row, false);
            $model->id = $row['variant_id'];
            if (!$model->save()) {
                $dataModel->addErrors($model->getErrors());
                break;
            }
            if ($row['is_new']) {
                $dataModel->is_new = (int) $row['is_new'];
            }
            $row['barcode'] = '';
            $row['main'] = 0;
            foreach ($barCodes as $i => $barRow) {
                if ($barRow['variant_id'] === $row['variant_id']) {
                    $row['barcode'] = $barRow['barcode'];
                    $row['main'] = $barRow['main'];
                    unset($barCodes[$i]);
                    break;
                }
            }
            Yii::$app->db->createCommand('INSERT INTO goods_variants (goods_id,variant_id,qty,qty_alt,is_new, barcode, main)VALUES(:goods_id,:vid,:qty,:qty_alt,:is_new,:barcode,:main)
                ON DUPLICATE KEY UPDATE qty=:qty,qty_alt=:qty_alt,is_new=:is_new, barcode=:barcode, main=:main', [
                ':goods_id' => $goods->id,
                ':vid' => $model->id,
                ':qty' => $row['qty'],
                ':qty_alt' => $row['qty_alt'],
                ':is_new' => $row['is_new'],
                ':barcode' => $row['barcode'],
                ':main' => $row['main'],
            ])->execute();
        }
        DataHelper::updateGoodsData($goods);

        self::clean($goods->id, $ids);
        return $dataModel;
    }

    static protected function clean($goods_id, $ids) {
        if (count($ids) > 0) {
            Yii::$app->db->createCommand()->delete('goods_variants', ['AND', 'goods_id=:goods_id', ['NOT IN', 'variant_id', $ids]], [':goods_id' => $goods_id])->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM goods_variants WHERE goods_id=:goods_id', [':goods_id' => $goods_id])->execute();
        }
    }

    public function getTitle() {
        if ($this->color === '' && $this->size === '') {
            return '';
        }
        if ($this->color <> '' && $this->size <> '') {
            return "{$this->size} / {$this->color}";
        }
        if ($this->color <> '') {
            return $this->color;
        }
        return $this->size;
    }

    public function initGoodsData($goods_id) {
        if ($this->qty === null) {
            $data = self::find()->from('goods_variants')->where(['goods_id' => $goods_id, 'variant_id' => $this->id])->asArray()->one();
            if ($data) {
                $this->qty = $data['qty'];
                $this->qty_alt = $data['qty_alt'];
                $this->is_new = $data['is_new'];
                $this->barCode = $data['barcode'];
            } else {
                $this->qty = 0;
                $this->qty_alt = 0;
                $this->is_new = 0;
                $this->barCode = '';
            }
        }
    }

    public function getQty() {
        return $this->qty;
    }

    public function getQtyAlt() {
        return $this->qty_alt;
    }

    public function getStockQty() {
        return $this->qty + $this->qty_alt;
    }

    public function getIsNewTxt() {
        return $this->is_new ? 'новинка' : '';
    }

    public function getIsNew() {
        return $this->is_new ? self::$valuesTrueFalse[1] : self::$valuesTrueFalse[0];
    }

    public function getIsNewRaw() {
        return $this->is_new;
    }

    public function isInStock() {
        return $this->qty > 0 && $this->qty >= $this->qty_alt;
    }

    public function isMinimum() {
        return $this->qty > 0 && $this->qty < $this->qty_alt;
    }

    public function isWait() {
        return $this->qty <= 0 && $this->qty_alt > 0;
    }

    public function isNone() {
        return $this->qty <= 0 && $this->qty_alt <= 0;
    }

    static public function getList($goods_id) {
        $goodsParams = self::find()->from('goods_variants')->where(['goods_id' => $goods_id])->indexBy('variant_id')->asArray()->all();
        $data = [];
        $query = self::find()->where(['in', 'id', array_keys($goodsParams)])->orderBy(['size' => SORT_ASC, 'color' => SORT_ASC]);
        foreach ($query->all() as $param) {
            $param->qty = $goodsParams[$param['id']]['qty'];
            $param->qty_alt = $goodsParams[$param['id']]['qty_alt'];
            $param->is_new = $goodsParams[$param['id']]['is_new'];
            $data[$param->id] = $param;
        }
        return $data;
    }

    static public function getListGrouped($goodsList, $public = true) {
        $query = self::find()->from('goods_variants')->where(['in', 'goods_id', array_keys($goodsList)]);
        if ($public) {
            $query->andWhere('qty>0 OR qty_alt>0');
        }
        $goodsParams = $query->indexBy('variant_id')->asArray()->all();
        $data = [];
        $query = self::find()->where(['in', 'id', array_keys($goodsParams)])->orderBy(['size' => SORT_ASC, 'color' => SORT_ASC]);
        foreach ($query->all() as $param) {
            $goodsData = &$goodsParams[$param['id']];
            $param->qty = $goodsData['qty'];
            $param->qty_alt = $goodsData['qty_alt'];
            $param->is_new = $goodsData['is_new'];
            $param->barCode = $goodsData['barcode'];
            $param->photoModel = $param->getPhotoModel($goodsList[$goodsData['goods_id']]);
            $param->goodsModel = $goodsList[$goodsData['goods_id']];
            $data[$param->size][$param->id] = $param;
        }
        return $data;
    }

    public function getPhotoModel($goodsModel) {
        $dataModel = GoodsPhotoData::find()->where(['goods_id' => $goodsModel->id, 'variant_id' => $this->id])->one();
        if ($dataModel === null) {
            return;
        }
        $data = $goodsModel->getModelFiles('photos')->findMulti(1, ['id' => $dataModel->id]);
        return array_shift($data);
    }

}
