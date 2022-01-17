<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class Params extends \app\components\BaseActiveRecord {

    const TYPE_GOODS_ONLY = 0;
    const TYPE_FILTER_ONLY = 1;
    const TYPE_GOODS_AND_FILTER = 2;
    const TYPE_HIDDEN = 3;

    static public $types = [
        self::TYPE_GOODS_ONLY => 'для карточки',
        self::TYPE_FILTER_ONLY => 'для фільтру',
        self::TYPE_GOODS_AND_FILTER => 'для карточки і фільтру',
        self::TYPE_HIDDEN => 'прихований',
    ];
    protected $value;
    protected $hash;

    public static function tableName() {
        return 'params';
    }

    public function rules() {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 45],
            [['unit'], 'string', 'max' => 10],
            [['type_id'], 'in', 'range' => array_keys(self::$types)],
            [['title', 'type_id'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels() {
        return [
            'title' => 'Найменування',
            'unit' => 'Одиниця виміру',
            'type_id' => 'Призначення',
        ];
    }

    public function search() {
        $query = self::find()->orderBy(['pos' => SORT_ASC, 'title' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query
                    ->andFilterWhere(['like', 'title', $this->title])
                    ->andFilterWhere(['=', 'type_id', $this->type_id]);
        }
        return $dataProvider;
    }

    static public function import($goods_id, $attrs) {
        $dataModel = new self();
        $ids = [];
        foreach ($attrs as $row) {
            $ids[] = $row['id'];
            $model = self::findModel($row['id']);
            $model->setAttributes($row, false);
            if (!$model->save()) {
                $dataModel->addErrors($model->getErrors());
                break;
            }
            if (!is_array($row['value'])) {
                $row['value'] = [$row['value']];
            }
            $value_ids = [];
            foreach ($row['value'] as $value) {
                $value_ids[] = $hash = md5($value);
                Yii::$app->db->createCommand('INSERT IGNORE INTO goods_params (goods_id,param_id,value,hash)VALUES(:goods_id,:param_id,:value,:hash)', [
                    ':goods_id' => $goods_id,
                    ':param_id' => $model->id,
                    ':value' => $value,
                    ':hash' => $hash,
                ])->execute();
            }
            self::cleanValues($goods_id, $row['id'], $value_ids);
        }
        self::clean($goods_id, $ids);
        return $dataModel;
    }

    static protected function clean($goods_id, $ids) {
        if (count($ids) > 0) {
            Yii::$app->db->createCommand()->delete('goods_params', ['AND', 'goods_id=:goods_id', ['NOT IN', 'param_id', $ids]], [':goods_id' => $goods_id])->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM goods_params WHERE goods_id=:goods_id', [':goods_id' => $goods_id])->execute();
        }
    }

    static protected function cleanValues($goods_id, $param_id, $ids) {
        if (count($ids) > 0) {
            Yii::$app->db->createCommand()->delete('goods_params', ['AND', 'goods_id=:goods_id AND param_id=:param_id', ['NOT IN', 'hash', $ids]], [':goods_id' => $goods_id, ':param_id' => $param_id])->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM goods_params WHERE goods_id=:goods_id AND param_id=:param_id', [':goods_id' => $goods_id, ':param_id' => $param_id])->execute();
        }
    }

    public function getTitle() {
        return $this->title;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function getValue() {
        return $this->value;
    }

    public function getValueTxt() {
        return "{$this->value} {$this->getUnit()}";
    }

    public function getHash() {
        return $this->hash;
    }

    public function getType() {
        return self::$types[$this->type_id];
    }

    static public function getList($goods_id, $type_id) {
        $goodsParams = self::find()->from('goods_params')->where(['goods_id' => $goods_id])->indexBy('param_id')->asArray()->all();
        $data = [];
        $types = [$type_id];
        if ($type_id == self::TYPE_GOODS_AND_FILTER) {
            $types[] = self::TYPE_FILTER_ONLY;
            $types[] = self::TYPE_GOODS_ONLY;
        }
        $query = self::find()->where(['in', 'id', array_keys($goodsParams)])->andWhere(['in', 'type_id', $types])->orderBy(['pos' => SORT_ASC]);
        foreach ($query->all() as $param) {
            $param->value = $goodsParams[$param['id']]['value'];
            $param->hash = $goodsParams[$param['id']]['hash'];
            $data[$param->id] = $param;
        }
        return $data;
    }

    static public function getFilterList($cat_id) {
        $goodsParams = [];
        $query = self::find()->from('goods_params')->where(new \yii\db\Expression('goods_id IN ( SELECT id FROM goods g WHERE cat_id=:cat_id AND ' . Goods::getPublicCondition() . ')'), [':cat_id' => $cat_id])->orderBy('value');
        foreach ($query->asArray()->all() as $row) {
            $goodsParams[$row['param_id']][$row['hash']] = $row['value'];
        }
        $data = [];
        $query = self::find()->where(['in', 'id', array_keys($goodsParams)])->andWhere(['in', 'type_id', [self::TYPE_FILTER_ONLY, self::TYPE_GOODS_AND_FILTER]])->orderBy(['pos' => SORT_ASC]);
        foreach ($query->asArray()->all() as $param) {
            $pid = &$param['id'];
            if (!isset($data[$pid])) {
                $unit = $param['unit'] == '' ? '' : ", {$param['unit']}";
                $data[$pid] = [
                    'title' => "{$param['title']}{$unit}",
                    'values' => []
                ];
            }
            $data[$pid]['values'] = $goodsParams[$pid];
        }
        return $data;
    }

}
