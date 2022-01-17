<?php

namespace app\modules\api\controllers;

use \Yii;
use app\components\Misc;
use app\models\Api;
use app\models\Goods;
use app\models\Brands;
use app\models\Category;
use app\models\Params;
use app\models\Variants;
use app\models\PriceTypes;
use app\models\GoodsPhotoData;
use app\models\DataHelper;
use app\models\Tasks;

class GoodsController extends A_Controller {

    public function actionSave() {
        $keys = array_diff(['id', 'title', 'article', 'code', 'variant_bar_codes', 'price', 'descr', 'qty_pack', 'weight', 'brand', 'brand_id', 'category', 'category_id', 'pic', 'params', 'photos', 'variants', 'prices', 'discount_group_id'], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        $this->isArray('variant_bar_codes', 'params', 'photos', 'variants', 'prices');
        $this->isValidArray([
            'params' => ['id', 'title', 'unit', 'value', 'type_id'],
            'photos' => ['id', 'variant_id', 'ext', 'data', 'main'],
            'variants' => ['variant_id', 'color', 'color_code', 'size', 'qty', 'qty_alt', 'is_new'],
            'prices' => ['price_type_id', 'price'],
            'variant_bar_codes' => ['variant_id', 'barcode', 'main'],
        ]);
        $res = Yii::transaction(function () {
                    $brand = Brands::import(Api::$data);
                    if ($brand->hasErrors()) {
                        Api::$message = 'Торговая марка ' . $brand->title . ': ' . implode(' ', $brand->getErrorsList());
                        return false;
                    }
                    $category = Category::import(Api::$data);
                    if ($category->hasErrors()) {
                        Api::$message = 'Категория ' . $category->title . ': ' . implode(' ', $category->getErrorsList());
                        return false;
                    }
                    Api::$data['cat_id'] = $category->id;
                    $goods = Goods::import(Api::$data);
                    if ($goods->hasErrors()) {
                        Api::$message = 'Товар ' . $goods->title . ': ' . implode(' ', $goods->getErrorsList());
                        return false;
                    }
                    $param = Params::import($goods->id, Api::$data['params']);
                    if ($param->hasErrors()) {
                        Api::$message = 'Параметр ' . $param->title . ': ' . implode(' ', $param->getErrorsList());
                        return false;
                    }
                    $variant = Variants::import($goods, Api::$data['variants'], Api::$data['variant_bar_codes']);
                    if ($variant->hasErrors()) {
                        Api::$message = "Вариант {$variant->getTitle()} ({$variant->id}): " . implode(' ', $variant->getErrorsList());
                        return false;
                    }
                    PriceTypes::import($goods->id, Api::$data['prices']);
                    $photoData = GoodsPhotoData::import($goods, Api::$data['photos']);
                    if ($photoData->hasErrors()) {
                        Api::$message = "Фото товара {$goods->code}: " . implode(' ', $photoData->getErrorsList());
                        return false;
                    }
                    $goods->has_new = $variant->getIsNewRaw();
                    $goods->update(false, ['has_new']);
                    return true;
                });
        if ($res) {
            Tasks::start(Tasks::REFRESH_CACHE, null, date('Y-m-d H:i:s', strtotime('+1 hour')));
        }
        $this->logName = Api::$data['code'];
        return ['res' => $res, 'message' => Api::$message];
    }

    public function actionStock() {
        $keys = array_diff(['stock', 'prices'], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        $this->isArray('stock', 'prices');
        $this->isValidArray([
            'stock' => ['id', 'variant_id', 'qty', 'qty_alt', 'price'],
            'prices' => ['id', 'price_type_id', 'price'],
        ]);

        $res = Yii::transaction(function () {
                    foreach (Api::$data['stock'] as $data) {
                        $ids[$data['id']] = $data['id'];
                        DataHelper::updateStock($data);
                    }
                    foreach (Api::$data['prices'] as $data) {
                        DataHelper::updatePrices($data);
                    }
                    return true;
                });
        Tasks::start(Tasks::AFTER_UPDATE_STOCK);
        $this->logName = 'stock';
        return ['res' => $res, 'message' => ''];
    }

    public function actionDelete() {
        $model = Goods::findOne(Api::$data);
        if ($model === null) {
            return ['res' => true, 'message' => ''];
        }
        $res = Yii::transaction(function () use ($model) {
                    $this->logName = $model->title;
                    return $model->deleteModel();
                });
        if ($res) {
            Tasks::start(Tasks::REFRESH_CACHE, null, date('Y-m-d H:i:s', strtotime('+1 hour')));
        }
        return ['res' => $res, 'message' => ''];
    }

}
