<?php

namespace app\models;

use Yii;

class XML_PriceUa extends \app\components\XmlDocument
{

    static public $attrs = [
        'name' => 'Номенклатура',
        'vendorCode' => 'Артикул',
        'code' => 'Штрихкод',
        'vendor' => 'Торгова марка',
        'visible_by_stock' => 'Залишок',
        'priceuah' => 'Гуртова',
        'image' => 'Фото',
        'odrder' => 'Замовлення',
        'categoryId' => 'Номенклатурна група',
        'param' => 'Параметри',
        'description' => 'Опис',
        'available' => 'Наявність',
    ];
    static public $default = ['name', 'vendorCode', 'code', 'vendor', 'visible_by_stock', 'priceuah', 'odrder'];
    static public $defaultWithImage = ['name', 'vendorCode', 'code', 'vendor', 'visible_by_stock', 'priceuah', 'image', 'odrder'];
    protected $attributes;
    protected $root;
    protected $params = [];

    public function createCatList()
    {
        $category = Category::findOne($this->attributes['cat_id']);
        $cats = $this->el('catalog');
        $cats->appendChild($this->el('category', $category->getTitle(), ['id' => $category->id]));
        foreach ($category->findChildren(null) as $model) {
            $parent = $model->parent()->one();
            $cats->appendChild($this->el('category', $model->getTitle(), ['id' => $model->id, 'parentId' => $parent->id]));
        }
        $this->root->appendChild($cats);
    }

    public function createGoodsList()
    {
        $goods = $this->el('items');
        $category = Category::findModel(@$this->attributes['cat_id']);
        $filter = new GoodsFilter($category);
        $modelsItems = Yii::$app->cache->get("_download_model_goods");

        foreach ($modelsItems as $model) {
            $item = $this->el('item', null, ['id' => $model->id, 'selling_type' => 'u']);
            if (isset($this->attributes['name'])) {
                $item->appendChild($this->el('name', $model->getTitle()));
            }
            if (isset($this->attributes['categoryId'])) {
                $item->appendChild($this->el('categoryId', $model->cat_id));
            }
            if (isset($this->attributes['priceuah'])) {
                $item->appendChild($this->el('priceuah', $model->getBasePrice()));
            }
            if (isset($this->attributes['image'])) {
                $photo = $model->getMainPhoto();
                if ($photo !== null) {
                    $item->appendChild($this->el('image', Yii::$app->request->getHostInfo() . $photo->getSrc()));
                }
            }
            if (isset($this->attributes['vendor'])) {
                $item->appendChild($this->el('vendor', $model->getBrand()->title));
            }
            if (isset($this->attributes['vendorCode'])) {
                $item->appendChild($this->el('vendorCode', $model->article));
            }
            if (isset($this->attributes['param'])) {
                foreach ($model->getParams(Params::TYPE_GOODS_ONLY) as $param) {
                    $item->appendChild($this->el('param', $param->getValue(), ['name' => $param->getTitle(), 'unit' => $param->getUnit()]));
                }
            }
            if (isset($this->attributes['description'])) {
                $item->appendChild($this->cdata('description', $model->getDescr()));
            }
            if (isset($this->attributes['available'])) {
                $item->appendChild($this->el('available', $model->getAvailableXml()));
            }
            $goods->appendChild($item);
        }
        $this->root->appendChild($goods);
    }

    static public function create($file, $filter = [])
    {
        try {
            $xml = new self($file);
            $xml->attributes = $filter;
            $xml->root = $xml->el('shop');
            $xml->createCatList();
            $xml->createGoodsList();
            $xml->dom->appendChild($xml->root);
            return $xml->saveFile();
        } catch (\Throwable $ex) {
            Yii::dump("{$ex->getFile()}({$ex->getLine()}):\n{$ex->getMessage()}");
            return false;
        }
    }

}
