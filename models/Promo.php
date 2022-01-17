<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

class Promo extends \app\components\BaseActiveRecord {

    use \app\components\T_FileAttributesMisc;

    static protected $actual;

    public function filesConfig() {
        return array(
            'cover' => [
                'scenario' => ModelFiles::SCENARIO_IMG, 'label' => 'Обкладинка 2400x800px', 'resize' => [
                    'big' => [1200, 400, true, '#ffffff'],
                    'medium' => [560, 300, true, '#ffffff'],
                ],
            ],
        );
    }

    public static function tableName() {
        return 'promo';
    }

    public function rules() {
        return [
            [['date_from', 'date_to'], 'default', 'value' => null],
            [['date_from', 'date_to'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels() {
        return [
            'date_from' => 'Дата початку',
            'date_to' => 'Дата закінчення',
        ];
    }

    public function search() {
        $query = self::find()->orderBy([new \yii\db\Expression('CASE WHEN date_to IS NULL THEN 9 ELSE date_to END DESC'), 'date_from' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            if ($this->date_from <> '') {
                $this->applyDateCriteria($query, 'date_from', $this->date_from);
            }
            if ($this->date_to <> '') {
                $this->applyDateCriteria($query, 'date_to', $this->date_to);
            }
        }
        return $dataProvider;
    }

    protected function applyDateCriteria($query, $attr, $date) {
        if (preg_match('/^\d{4}$/', $date)) {
            $query->andWhere("{$attr} LIKE :{$attr}", [":{$attr}" => "{$date}%"]);
        } elseif (preg_match('/^(\d{2})\.(\d{4})$/', $date, $matches)) {
            $query->andWhere("{$attr} LIKE :{$attr}", [":{$attr}" => "{$matches[2]}-{$matches[1]}-%"]);
        } elseif (preg_match('/^\d{2}$/', $date)) {
            $query->andWhere("{$attr} LIKE :{$attr}", [":{$attr}" => date("Y-{$date}-%")]);
        } elseif (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) {
            $query->andWhere("{$attr} LIKE :{$attr}", [":{$attr}" => date('Y-m-d', strtotime($date))]);
        }
    }

    static public function import($attrs) {
        $model = self::findModel($attrs['id']);
        $model->setAttributes($attrs, false);
        $res = $model->save();
        if ($res) {
            $model->saveGoods($attrs['goods']);
        }
        return $model;
    }

    protected function saveGoods($data) {
        $ids = [];
        foreach ($data as $row) {
            $ids[] = $row['id'];
            if (Goods::find()->where(['id' => $row['id']])->exists() === false) {
                $this->addError('id', "Товара и идентификатором {$row['id']} не существует.");
                break;
            }
            Yii::$app->db->createCommand('INSERT INTO promo_goods (id,goods_id,discount)VALUES(:id,:goods_id,:discount) ON DUPLICATE KEY UPDATE discount=:discount', [':id' => $this->id, ':goods_id' => $row['id'], ':discount' => $row['discount']])->execute();
        }
        if (count($ids) > 0) {
            Yii::$app->db->createCommand()->delete('promo_goods', ['AND', 'id=:id', ['NOT IN', 'goods_id', $ids]], [':id' => $this->id])->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM promo_goods WHERE id=:id', [':id' => $this->id])->execute();
        }
    }

    public function saveModel() {
        $res = $this->save();
        return $res;
    }

    public function getDateFrom() {
        if ($this->date_from === null) {
            return '';
        }
        return date('d.m.Y', strtotime($this->date_from));
    }

    public function getDateTo() {
        if ($this->date_to === null) {
            return '';
        }
        return date('d.m.Y', strtotime($this->date_to));
    }

    public function getRange() {
        if ($this->date_from === null && $this->date_to === null) {
            return 'безстрокова';
        }
        if ($this->date_from !== null && $this->date_to !== null) {
            return "з {$this->getDateFrom()} по {$this->getDateTo()}";
        }
        if ($this->date_from !== null) {
            return "з {$this->getDateFrom()}";
        } else {
            return "по {$this->getDateTo()}";
        }
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

    static public function getActualCriteria($id = null) {
        $query = self::find();
        if ($id !== null) {
            $query->where(['id' => $id]);
        }
        $query
                ->andWhere('date_from<=NOW() AND CONCAT(date_to," 23:59:59")>=NOW()
            OR date_from<=NOW() AND date_to IS NULL
            OR date_from IS NULL AND CONCAT(date_to," 23:59:59")>=NOW()
            OR date_from IS NULL AND date_to IS NULL')
                ->andWhere('filter_params IS NOT NULL')
        ;
        return $query;
    }

    static public function initActual() {
        if (self::$actual === null) {
            self::$actual = self::getActualCriteria()->indexBy('id')->all();
        }
        return self::$actual;
    }

    static public function findActual() {
        return self::initActual();
    }

    static public function findOneActual($id) {
        return self::getActualCriteria($id)->one();
    }

    static public function getDiscount($goods_id) {
        return (float) (new \yii\db\Query)->select(new \yii\db\Expression('MAX(discount)'))
                        ->from('promo_goods')
                        ->where(['goods_id' => $goods_id])
                        ->andWhere(['IN', 'id', array_keys(self::initActual())])
                        ->scalar();
    }

    public function getUrl() {
        $params = (array) json_decode($this->filter_params);
        $params[0] = '/client/catalog/promo';
        $params['id'] = $this->id;
        return Url::toRoute($params);
    }

}
