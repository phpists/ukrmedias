<?php

namespace app\models;

use yii\helpers\Url;
use yii\helpers\BaseFileHelper;
use Yii;
use app\components\Misc;
use app\components\AutoLogin;
use app\components\TurboSms;

class DataHelper {

    const HASH_KEY = 'qJxzh5dxs5eRtuO0Skl2peeEjmhgl4IG';
    const PAGE_SIZE = 20;

    static public $user;

    static public function unsetLimits() {
        set_time_limit(0);
        ini_set('memory_limit', '4G');
    }

    static public function updateStock($data) {
        Yii::$app->db->createCommand()->update('goods', ['price' => $data['price']], ['id' => $data['id']])->execute();
        Yii::$app->db->createCommand()->update('goods_variants', ['qty' => $data['qty'], 'qty_alt' => $data['qty_alt']], ['goods_id' => $data['id'], 'variant_id' => $data['variant_id']])->execute();
    }

    static public function updatePrices($data) {
        Yii::$app->db->createCommand()->update('goods_prices', ['price' => $data['price']], ['goods_id' => $data['id'], 'price_type_id' => $data['price_type_id']])->execute();
    }

    static public function setPromoGoods($id = null) {
        $promoModels = $id === null ? Promo::initActual() : [$id => Promo::findOne($id)];
        $actual = array_keys($promoModels);
        if (count($actual) === 0) {
            Goods::updateAll(['has_promo' => 0]);
            Promo::updateAll(['filter_params' => null]);
            return;
        }
        Misc::iterator(Goods::find(), function ($dataModel) use ($actual) {
            $hasPromo = (new \yii\db\Query)->select(new \yii\db\Expression('1'))->from('promo_goods')->where(['goods_id' => $dataModel->id])->andWhere(['IN', 'id', $actual])->limit(1)->scalar();
            $dataModel->updateAttributes(['has_promo' => $hasPromo]);
        });
        foreach ($promoModels as $dataModel) {
            $params = [];
            $goodsIds = (new \yii\db\Query)->select('goods_id')->from('promo_goods')->where(['id' => $dataModel->id])->column();
            $brandIds = (new \yii\db\Query)->select('brand_id')->distinct()->from(['g' => 'goods'])->where(['IN', 'id', $goodsIds])->andWhere(Goods::getPublicCondition())->andWhere('has_promo=1')->column();
            $catIds = (new \yii\db\Query)->select('cat_id')->distinct()->from(['g' => 'goods'])->where(['IN', 'id', $goodsIds])->andWhere(Goods::getPublicCondition())->andWhere('has_promo=1')->column();
            if (count($brandIds) > 0) {
                $params[GoodsFilter::$brands] = $brandIds;
            }
            if (count($catIds) > 0) {
                $params[GoodsFilter::$cats] = $catIds;
            }
            if (count($params) > 0) {
                $dataModel->updateAttributes(['filter_params' => json_encode($params)]);
            } else {
                $dataModel->updateAttributes(['filter_params' => null]);
            }
            self::setPromoData($dataModel->id);
        }
    }

    static public function closeOrders() {
        Misc::iterator(Orders::find()->where(['status_id' => Orders::STATUS_READY])->andWhere('status_date=NOW()-INTERVAL 9 DAY'), function ($dataModel) {
            $firm = Firms::findOne($dataModel->firm_id);
            foreach (Users::find()->select('email')->where(['firm_id' => $dataModel->firm_id, 'notify' => 1, 'active' => 1])->andWhere(['<>', 'email', ''])->column() as $email) {
                Letters::setupTask([
                    'to' => $email,
                    'subject' => "Зміна статусу замовлення №{$dataModel->getNumber()}",
                    'body' => "По замовленню №{$dataModel->getNumber()} завтра буде змінено статус з \"Готовий до відправлення\" на \"Виконано\".<br/>
                        Якщо виникли питання зверніться, будь-ласка, до Вашого менеджера.<br/>
                        Ваш менеджер: {$firm->manager_name}, телефон {$firm->manager_phone}.<br/>
                        Дякуємо за співпрацю.",
                ]);
            }
        });
        Misc::iterator(Orders::find()->where(['status_id' => Orders::STATUS_READY])->andWhere('status_date<=NOW()-INTERVAL 10 DAY'), function ($dataModel) {
            $oldStatusTxt = $dataModel->getStatus();
            $dataModel->updateAttributes(['status_id' => Orders::STATUS_FINISHED, 'status_date' => date('Y-m-d')]);
            self::notifyOrderStatus($dataModel, $oldStatusTxt);
        });
    }

    static public function clean() {
        TurboSms::clean();
        foreach (BaseFileHelper::findFiles(Yii::$app->getRuntimePath() . '/attachments') as $file) {
            if (filemtime($file) < strtotime('-27 day')) {
                unlink($file);
            }
        }
        Yii::$app->db->createCommand('DELETE FROM letters WHERE date<NOW()-INTERVAL 20 DAY')->execute();
        Yii::$app->db->createCommand('DELETE FROM tasks WHERE date<NOW()-INTERVAL 10 DAY')->execute();
        if (date('md') === '0101') {
            Yii::$app->db->createCommand('TRUNCATE users_actions')->execute();
            Yii::$app->db->createCommand('ALTER TABLE users_actions AUTO_INCREMENT = 1')->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM users_actions WHERE date<NOW()-INTERVAL 15 DAY')->execute();
        }
    }

    static public function notifyOrderStatus($order, $oldStatusTxt) {
        $url = 'https://' . getenv('SERVER_NAME') . '/client/orders/view?id=' . $order->id;
        foreach (Users::find()->select('email')->where(['firm_id' => $order->firm_id, 'notify' => 1, 'active' => 1])->andWhere(['<>', 'email', ''])->column() as $email) {
            Letters::setupTask([
                'to' => $email,
                'subject' => "Зміна статусу замовлення №{$order->getNumber()}",
                'body' => "Шановний партнер!<br/>Cтатус Вашого змовлення №{$order->getNumber()} змінений з \"{$oldStatusTxt}\" на \"{$order->getStatus()}\".<br/>Деталі замовлення за посиланням <a href=\"{$url}\">{$url}</a>.",
            ]);
        }
    }

    static public function getCategoryData($cat_id) {
        $data = Yii::$app->cache->get("_category_view_data_{$cat_id}");
        if ($data === false) {
            $category = Category::findOne($cat_id);
            $data = self::setCategoryData($category);
        }
        return $data;
    }

    static public function setCategoryData($category) {
        $catIds = $category->getIdsDown(true, false);
        $brandIds = Goods::find()->select('brand_id')->distinct()->alias('g')->where(['in', 'cat_id', $catIds])->andWhere(Goods::getPublicCondition())->column();
        $brandIdsNovelty = Goods::find()->select('brand_id')->distinct()->alias('g')->where(['in', 'cat_id', $catIds])->andWhere(Goods::getPublicCondition())->andWhere('has_new=1')->column();
        Yii::$app->db->createCommand('UPDATE category SET visible_by_goods=:v WHERE id=:id', [':v' => count($brandIds) > 0, ':id' => $category->id])->execute();
        foreach ($brandIds as $brand_id) {
            Yii::$app->db->createCommand('INSERT IGNORE INTO brand_cats_visibility (brand_id, cat_id) VALUES(:brand_id,:cat_id)', [':brand_id' => $brand_id, ':cat_id' => $category->id])->execute();
        }
        Yii::$app->db->createCommand()->delete('brand_cats_visibility', ['AND', 'cat_id=:cat_id', ['NOT IN', 'brand_id', $brandIds]], ['cat_id' => $category->id])->execute();
        $data = [
            'brands' => Brands::keyvalByIds($brandIds),
            'params' => Params::getFilterList($category->id),
            'catsNovelty' => Category::getGroupedList('has_new=1'),
            'brandsNovelty' => Brands::keyvalByIds($brandIdsNovelty),
        ];
        Yii::$app->cache->set("_category_view_data_{$category->id}", $data, 0);
        return $data;
    }

    static public function setPromoData($promo_id) {
        if ($promo_id <> '') {
            $goodsIds = (new \yii\db\Query)->select('goods_id')->from('promo_goods')->where(['id' => $promo_id])->column();
            $brandIds = (new \yii\db\Query)->select('brand_id')->distinct()->from(['g' => 'goods'])->where(['IN', 'id', $goodsIds])->andWhere(Goods::getPublicCondition())->andWhere('has_promo=1')->column();
            $catIds = (new \yii\db\Query)->select('cat_id')->distinct()->from(['g' => 'goods'])->where(['IN', 'id', $goodsIds])->andWhere(Goods::getPublicCondition())->andWhere('has_promo=1')->column();
        } else {
            $brandIds = (new \yii\db\Query)->select('brand_id')->distinct()->from(['g' => 'goods'])->andWhere(Goods::getPublicCondition())->andWhere('has_promo=1')->column();
            $catIds = (new \yii\db\Query)->select('cat_id')->distinct()->from(['g' => 'goods'])->andWhere(Goods::getPublicCondition())->andWhere('has_promo=1')->column();
        }
        $data = [
            'brands' => Brands::keyvalByIds($brandIds),
            'cats' => Category::getGroupedList('has_promo=1', $catIds),
        ];
        Yii::$app->cache->set("_promo_view_data_{$promo_id}", $data, 0);
        return $data;
    }

    static public function getPromoData($id) {
        $data = Yii::$app->cache->get("_promo_view_data_{$id}");
        if ($data === false) {
            $data = self::setPromoData($id);
        }
        return $data;
    }

    static public function getNoveltyQty() {
        return Goods::find()->select('COUNT(*)')->alias('g')->where(Goods::getPublicCondition())->andWhere('id IN(SELECT goods_id FROM goods_variants WHERE is_new=1)')->cache(HTML_CACHE_DURATION)->scalar();
    }

    static public function updateGoodsData($model) {
        $qty = 0;
        $prices = [$model->price];
        foreach (Variants::getListGrouped([$model->id => $model], false) as $size => $variants) {
            foreach ($variants as $variant) {
                $qty += $variant->qty;
                $qty += $variant->qty_alt;
            }
        }
        foreach ($model->findGroupGoods(false) as $dataModel) {
            $prices[] = $dataModel->price;
        }
        $model->setVisibleByStock($qty, min($prices), max($prices));
    }

}
