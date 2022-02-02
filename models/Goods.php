<?php

namespace app\models;

use app\components\Auth;
use app\components\Misc;
use Yii;
use yii\data\ActiveDataProvider;

class Goods extends \app\components\BaseActiveRecord
{

    use \app\components\T_CpuModel;
    use \app\components\T_FileAttributesMisc;

    const INC_TYPE_QTY = 0;
    const INC_TYPE_PACK = 1;

    static public $price_type_id;
    public $typedPrice;
    public $discount;
    public $size;
    public $color;
    public $variant_id;
    protected $_price;
    protected $_old_price;
    protected $_promo_discount;

    public function filesConfig()
    {
        return array(
            'photos' => [
                'scenario' => ModelFiles::SCENARIO_IMG, 'multi' => true, 'label' => 'Зображення 750x1000', 'default' => '/files/images/system/empty.png', 'resize' => [
                    'big' => [430, 500],
                    'medium' => [210, 280],
                    'small' => [100, 100],
                    'color' => [36, 36],
                ],
            ],
        );
    }

    public static function tableName()
    {
        return 'goods';
    }

    public function rules()
    {
        return [
            [['title', 'descr', 'price', 'qty_pack', 'weight', 'brand', 'brand_id', 'cat_id'], 'required'],
            ['pic', 'default', 'value' => ''],
            [['title'], 'string', 'max' => 255],
            [['code', 'pic'], 'string', 'max' => 50],
            [['article'], 'string', 'max' => 45],
            [['descr'], 'string', 'max' => 65535],
            [['video_1', 'video_2'], 'match', 'pattern' => '@^<iframe[^<]+src\="[^"]+/embed/[^"]+"[^<]+</iframe>$@'],
            [['volume'], 'default', 'value' => 0],
            [['price', 'qty_pack', 'weight', 'volume'], 'filter', 'filter' => ['\app\components\Misc', 'priceFilter']],
            [['price', 'qty_pack', 'weight'], 'number', 'min' => 0.01],
            //[['volume'], 'number', 'min' => 0.000001],
            ['brand_id', 'exist', 'targetClass' => 'app\\models\\Brands', 'targetAttribute' => 'id'],
            ['cat_id', 'exist', 'targetClass' => 'app\\models\\Category', 'targetAttribute' => 'id'],
            [['discount_group_id'], 'safe'],
            [['title', 'article', 'code', 'brand', 'size', 'color'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Найменування',
            'article' => 'Артикул',
            'code' => 'Код',
            'descr' => 'Опис',
            'price' => 'Базова ціна',
            'typedPrice' => 'Ціна',
            'discount' => 'Знижка',
            'qty_pack' => 'Кількість в уп.',
            'weight' => 'Вага брутто, кг',
            'volume' => 'Об`єм, куб.м',
            'brand' => 'Торгова марка',
            'brand_id' => 'Торгова марка',
            'cat_id' => 'Категорія',
            'video_1' => 'Код відео <iframe> №1',
            'video_2' => 'Код відео <iframe> №2',
            'pic' => 'Номер малюнку',
            'size' => 'Розмір',
            'color' => 'Колір',
        ];
    }

    static public function getPublicCondition()
    {
        return 'g.price>0 AND visible_by_stock=1';
    }

    public function search()
    {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query->andFilterWhere(['like', 'title', $this->title]);
            $query->andFilterWhere(['like', 'article', $this->article]);
            $query->andFilterWhere(['like', 'code', $this->code]);
            $query->andFilterWhere(['like', 'brand', $this->brand]);
            $query->andFilterWhere(['like', 'size', $this->size]);
            $query->andFilterWhere(['like', 'color', $this->color]);
        }
        return $dataProvider;
    }

    public function searchByPriceType($pid)
    {
        $dataProvider = $this->search();
        $dataProvider->query->select(new \yii\db\Expression('g.*, gp.price AS typedPrice'));
        $dataProvider->query->alias('g');
        $dataProvider->query->andWhere('id IN(SELECT goods_id FROM goods_prices WHERE price_type_id=:pid)', [':pid' => $pid]);
        $dataProvider->query->leftJoin(['gp' => 'goods_prices'], 'g.id=gp.goods_id');
        return $dataProvider;
    }

    public function searchByPromo($pid)
    {
        $dataProvider = $this->search();
        $dataProvider->query->select(new \yii\db\Expression('g.id, title, article,code, price, gp.discount'));
        $dataProvider->query->alias('g');
        $dataProvider->query->andWhere('g.id IN(SELECT goods_id FROM promo_goods WHERE id=:pid)', [':pid' => $pid]);
        $dataProvider->query->leftJoin(['gp' => 'promo_goods'], 'g.id=gp.goods_id');
        return $dataProvider;
    }

    public function searchWithVariants()
    {
        new \yii\data\ActiveDataProvider;
        $dataProvider = $this->search();
        $dataProvider->query->select(new \yii\db\Expression('CONCAT(g.id,"-",gv.variant_id) AS id, g.title, g.article, g.code, g.price, gv.variant_id, v.size, v.color'));
        $dataProvider->query->alias('g');
        $dataProvider->query->rightJoin(['gv' => 'goods_variants'], 'g.id=gv.goods_id');
        $dataProvider->query->leftJoin(['v' => 'variants'], 'gv.variant_id=v.id');
        return $dataProvider;
    }

    static public function import($attrs)
    {
        $model = Goods::findModel($attrs['id']);
        $model->saveModel($attrs, 'import');
        $model->addErrors($model->CpuModel->getErrors());
//        if ($model->hasErrors()) {
//            Yii::dump($model->id, $model->CpuModel->getScenario(), $model->getErrors());
//        }
        return $model;
    }

    public function saveModel($attrs, $scenario = null)
    {
        $this->setAttributes($attrs, false);
        $this->getCpuModel(true);
        if ($scenario !== null) {
            $this->getCpuModel()->setScenario($scenario);
        }
        $res = $this->validate() && $this->CpuModel->validate();
        if (!$res) {
            return $res;
        }
        $res = $this->save(false);
        if ($res) {
            $res = $this->CpuModel->saveModel($this);
        }
        return $res;
    }

//    static public function getPriceInfo($goods_id) {
//        $model = self::findModel($goods_id);
//        $goods = self::find()->alias('g')->where(['cat_id' => $model->cat_id, 'brand_id' => $model->brand_id, 'pic' => $model->pic])->andWhere('pic>0 AND ' . self::getPublicCondition())->all();
//        $prices = [];
//        foreach ($goods as $dataModel) {
//            $prices[] = $dataModel->getPrice();
//        }
//        $min = min($prices);
//        $max = max($prices);
//        if ($min <> $max) {
//            return "{$min} - {$max}";
//        }
//        return $min;
//    }

    public function updateModel()
    {
        $res = $this->validate() && $this->getCpuModel()->validate();
        if (!$res) {
            return $res;
        }
        $res = $this->save(false, ['video_1', 'video_2']);
        if ($res) {
            $this->CpuModel->visible = $_POST['Cpu']['visible'] ?? 1;
            $res = $this->CpuModel->saveModel($this);
        }
        return $res;
    }

    public function deleteModel()
    {
        $res = is_numeric($this->delete());
        if ($res) {
            Cpu::deleteAll(['id' => $this->id, 'class' => get_called_class()]);
        }
        return $res;
    }

    public function getBrand()
    {
        $key = "_brand_{$this->brand_id}";
        $value = Yii::$app->cache->get($key);
        if (!$value) {
            $value = $this->hasOne(Brands::className(), ['id' => 'brand_id'])->one();
            Yii::$app->cache->set($key, $value);
        }
        return $value;
    }

    public function getCategory()
    {
        $key = "_category_{$this->cat_id}";
        $value = Yii::$app->cache->get($key);
        if (!$value) {
            $value = $this->hasOne(Category::className(), ['id' => 'cat_id'])->one();
            Yii::$app->cache->set($key, $value);
        }
        return $value;
    }

    public function getParams($type_id = Params::TYPE_GOODS_AND_FILTER)
    {
        return Params::getList($this->id, $type_id);
    }

    public function getVariants()
    {
        return Variants::getList($this->id);
    }

    public function findGroupGoods($self = true)
    {
        $goods = self::find()->alias('g')
            ->where(['brand_id' => $this->brand_id, 'cat_id' => $this->cat_id, 'pic' => $this->pic])
            ->andWhere('id<>:id AND ' . self::getPublicCondition(), [':id' => $this->id])
            ->indexBy('id')->all();
        if ($self) {
            $goods[$this->id] = $this;
        }
        return $goods;
    }

    public function getVariantsGrouped()
    {
        return Variants::getListGrouped($this->findGroupGoods());
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescr()
    {
        return $this->descr;
    }

    public function getPrice()
    {
        if ($this->_price === null) {
            if (Auth::isClient()) {
                self::$price_type_id = Yii::$app->user->identity->getFirm()->price_type_id;
            }
            if (self::$price_type_id !== null) {
                $this->_price = Yii::$app->db->createCommand('SELECT price FROM goods_prices WHERE goods_id=:goods_id AND price_type_id=:pid', [':goods_id' => $this->id, ':pid' => self::$price_type_id])->queryScalar();
            }
            if (!$this->_price) {
                $this->_price = $this->price;
            }
            $this->_old_price = Misc::round($this->_price);
            $this->applyPromo();
            $this->_price = Misc::round($this->_price);
        }
        return $this->_price;
    }

    public function getPriceRange()
    {
        $goods = $this->findGroupGoods(false);
        $prices = [$this->getPrice()];
        foreach ($goods as $dataModel) {
            $prices[] = $dataModel->getPrice();
        }
        $min = min($prices);
        $max = max($prices);
        if ($min <> $max) {
            return "{$min} - {$max}";
        }
        return $min;
    }

    protected function applyPromo()
    {
        if (!$this->has_promo) {
            return;
        }
        if ($this->_promo_discount === null) {
            $this->_promo_discount = Promo::getDiscount($this->id);
        }
        $newPrice = $this->price * (100 - $this->_promo_discount) / 100;
        if ($newPrice < $this->_price) {
            $this->_price = $newPrice;
        }
    }

    public function hasOldPrice()
    {
        $this->getPrice(); //init price
        return $this->_old_price <> $this->_price;
    }

    public function getOldPrice()
    {
        return $this->_old_price;
    }

    public function getRuntimeDiscount()
    {
        if ($this->_old_price > 0) {
            return round(100 * ($this->_old_price - $this->_price) / $this->_old_price, 0);
        } else {
            return 0;
        }
    }

    public function getBasePrice()
    {
        if ($this->price_min != $this->price_max){
            $price = $this->price_min . ' ' . $this->price_max;
        } else {
            $price = $this->price;
        }

        return $price;
    }

    public function getDiscountTxt()
    {
        if ($this->price == 0) {
            return '0%';
        }
        return round(100 * ($this->price - $this->typedPrice) / $this->price, 2) . '%';
    }

    public function getPromoDiscountTxt()
    {
        return "{$this->discount}%";
    }

    public function getMainPhoto()
    {
        $dataModel = GoodsPhotoData::find()->where(['goods_id' => $this->id, 'main' => 1])->one();
        if ($dataModel === null) {
            $dataModel = GoodsPhotoData::find()->where(['goods_id' => $this->id])->one();
        }
        if ($dataModel === null) {
            return;
        }
        $data = $this->getModelFiles('photos')->findMulti(1, ['id' => $dataModel->id]);
        return array_shift($data);
    }

    public function getPhotos()
    {
        return $this->getModelFiles('photos')->findMulti();
    }

    public function isHasNew()
    {
        return $this->has_new > 0;
    }

    public function isHasPromo()
    {
        return $this->has_promo > 0;
    }

    public function getAvailableXml()
    {
        $data = Yii::$app->db->createCommand('SELECT SUM(qty) AS qty, SUM(qty_alt) FROM goods_variants WHERE goods_id=:goods_id', [':goods_id' => $this->id])->queryOne();
        if ($data['qty'] > 0) {
            return 'true';
        } elseif ($data['qty_alt'] > 0) {
            return 'false';
        } else {
            return '';
        }
    }

    public function getAvailableXls()
    {
        $data = Yii::$app->db->createCommand('SELECT SUM(qty) AS qty, SUM(qty_alt) AS qty_alt FROM goods_variants WHERE goods_id=:goods_id', [':goods_id' => $this->id])->queryOne();
        if ($data['qty'] > 0) {
            return 'в наявності';
        } elseif ($data['qty_alt'] > 0) {
            return '3 дні';
        } else {
            return 'відсутній';
        }
    }

    public function setVisibleByStock($qty, $min, $max)
    {
        $v = intval($qty > 0);
        $this->updateAttributes(['visible_by_stock' => $v, 'price_min' => $min, 'price_max' => $max]);
        $this->getCpuModel()->updateAttributes(['visible' => $v]);
    }

    public static function fasetsByBrands($brands, $category_id)
    {
        $query = (new \yii\db\Query())->from('goods_params as gp')
            ->select('gp.param_id, gp.value, gp.hash')
            ->innerJoin('goods g', 'gp.goods_id = g.id')
            ->where([
                'g.cat_id' => $category_id,
                'g.visible_by_stock' => 1,
                'gp.param_id' => ["Color", "Size"],
            ])
            ->groupBy('gp.param_id, gp.value');

        if (count($brands)) {
            $query->andWhere([
                'g.brand_id' => $brands,
            ]);
        }

        $facets = $query->all();

        $arr = [];
        foreach ($facets as $item) {
            $key = $item['param_id'];
            $value = $item['value'];
            $hash = $item['hash'];

            $arr[$key][] = [
                'id' => $hash,
                'value' => $value
            ];
        }


        return $arr;
    }

    public function getVisibleStockCount()
    {
       $goodSizes = $this->getVariantsGrouped();

       $i = 0;
       foreach ($goodSizes as $size){
           foreach ($size as $item){
               if (isset($item)){
                   $i+= ($item->qty_alt + $item->qty);
               }
           }
       }

       return $i;
    }

    public function getVisibleStockItems()
    {
        $values = [];
        foreach ($this->getVariantsGrouped() as $goodSizes) {
            foreach ($goodSizes as $size) {

                $values[] = [
                    $size['size'] . ' ' . $size['color'],
                    '',
                    (string)$size->barCode,
                    '',
                    $size->qty_alt + $size->qty,
                    $size->goodsModel->price,
                    ''
                ];

            }
        }

        return $values;
    }


}
