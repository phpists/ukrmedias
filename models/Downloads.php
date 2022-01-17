<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class Downloads extends \app\components\BaseActiveRecord {

    use \app\components\T_FileAttributesMisc;

    const TYPE_MISC = '0';
    const TYPE_CATALOG = '1';
    const TYPE_CERTIFICATE = '2';

    static public $typeLabels = [
        self::TYPE_CATALOG => 'каталоги',
        self::TYPE_CERTIFICATE => 'сертифікати',
        self::TYPE_MISC => 'інше',
    ];

    public function filesConfig() {
        return array(
            'cover' => [
                'scenario' => ModelFiles::SCENARIO_IMG, 'label' => 'Обкладинка 250x350px', 'resize' => [
                    'small' => [250, 350, true, '#ffffff'],
                ],
            ],
            'file' => ['scenario' => ModelFiles::SCENARIO_DOCS, 'label' => 'Файл'],
        );
    }

    public static function tableName() {
        return 'downloads';
    }

    public function rules() {
        return [
            [['title', 'brand_id', 'type_id'], 'required'],
            [['title'], 'string', 'max' => 255],
            ['type_id', 'in', 'range' => array_keys(self::$typeLabels)],
            ['brand_id', 'exist', 'targetClass' => 'app\\models\\Brands', 'targetAttribute' => 'id'],
            [['title', 'brand_id', 'type_id'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels() {
        return [
            'title' => 'Найменування',
            'brand_id' => 'Торгова марка',
            'type_id' => 'Група документів',
        ];
    }

    public function search() {
        $query = self::find()->orderBy(['title' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query->andFilterWhere(['=', 'type_id', $this->type_id]);
            $query->andFilterWhere(['like', 'title', $this->title]);
            if ($this->brand_id <> '') {
                $query->andWhere('brand_id IN (SELECT id FROM brands WHERE title LIKE :btitle)', [':btitle' => '%' . $this->brand_id . '%']);
            }
        }
        return $dataProvider;
    }

    public function searchClient() {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query->andFilterWhere(['=', 'type_id', $this->type_id]);
            if ($this->brand_id <> '') {
                $query->andWhere(['brand_id' => $this->brant_id]);
            }
        }
        return $dataProvider;
    }

    public function saveModel() {
        $res = $this->save();
        return $res;
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

    public function getType() {
        return self::$typeLabels[$this->type_id];
    }

    public function getBrand() {
        $key = "_brand_{$this->brand_id}";
        $value = Yii::$app->cache->get($key);
        if (!$value) {
            $value = $this->hasOne(Brands::className(), ['id' => 'brand_id'])->one();
            Yii::$app->cache->set($key, $value);
        }
        return $value;
    }

    public function getBrandTitle() {
        return $this->brand->title;
    }

    static public function getBrands() {
        $ids = self::find()->select('brand_id')->distinct()->column();
        return Brands::keyvalByIds($ids);
    }

}
