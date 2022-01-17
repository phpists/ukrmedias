<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class PriceTypes extends \app\components\BaseActiveRecord {

    public static function tableName() {
        return 'price_types';
    }

    public function rules() {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 45],
            [['title'], 'safe', 'on' => 'search'],
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
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query
                    ->andFilterWhere(['like', 'title', $this->title]);
        }
        return $dataProvider;
    }

    static public function importModel($attrs) {
        $model = self::findModel($attrs['id']);
        $model->setAttributes($attrs, false);
        $model->save();
        return $model;
    }

    static public function import($goods_id, $attrs) {
        $ids = [];
        foreach ($attrs as $row) {
            $ids[] = $row['price_type_id'];
            $model = self::findModel($row['price_type_id']);
            if ($model->isNewRecord) {
                $model->id = $row['price_type_id'];
                $model->title = $row['price_type_id'];
                $model->save(false);
            }
            Yii::$app->db->createCommand('INSERT INTO goods_prices (goods_id,price_type_id,price)VALUES(:goods_id,:price_type_id,:price) ON DUPLICATE KEY UPDATE price=:price', [
                ':goods_id' => $goods_id,
                ':price_type_id' => $row['price_type_id'],
                ':price' => $row['price'],
            ])->execute();
        }
        self::clean($goods_id, $ids);
    }

    static protected function clean($goods_id, $ids) {
        if (count($ids) > 0) {
            Yii::$app->db->createCommand()->delete('goods_prices', ['AND', 'goods_id=:goods_id', ['NOT IN', 'price_type_id', $ids]], [':goods_id' => $goods_id])->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM goods_prices WHERE goods_id=:goods_id', [':goods_id' => $goods_id])->execute();
        }
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

}
