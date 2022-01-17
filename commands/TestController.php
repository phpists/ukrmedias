<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use \Yii;
use app\models\Api;
use app\models\Firms;
use app\models\Goods;
use app\models\Params;
use app\models\Orders;
use app\models\Export;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TestController extends Controller {

    static protected function dataset($data) {
        //$data = preg_replace('/\r\n/', '', $data);
        //$data = str_replace('\r\n', '', $data);
        //Yii::dumpAlt($data);
        $data = json_decode($data, true);
        //Yii::dumpAlt($data);
        return json_decode($data['dataset'], true);
    }

    public function actionTmp() {
        $file = Yii::$app->getRuntimePath() . '/goods.json';
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/goods/save', $data, 'POST');
        Yii::dump(__METHOD__, $r);
    }

    public function actionFirm() {
        $id = 'test-id-1s';
        $data = [
            'id_1s' => $id,
            'title' => 'Test API Firm 2',
            'phones' => ['+38 050 123-45-67', '+380990000001', '+380990000002'],
            'price_type_id' => '1',
            'delivery_ukrmedias' => true,
            'discount_groups' => [['id' => '2', 'title' => 'discount 2', 'discount' => '20'],],
            'groups' => [
                ['id' => '1', 'title' => 'FirmGroup New'],
            ],
            'manager_name' => 'mgr name',
            'manager_phone' => 'mgr phone',
            'manager_mrt' => 'мgr_mrt',
            'filter_status' => 1,
            'filter_assortment' => 1,
            'filter_tt' => 1,
            'filter_activity' => 1,
            'filter_discipline' => 1,
        ];
        #$data = self::dataset('');
        #Yii::dump(__METHOD__, $data);
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/firms/save', $data, 'POST');
        Yii::dump(__METHOD__, $r);
//        $data = $id;
//        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/firms/delete', $data, 'POST');
//        Yii::dump(__METHOD__, $r);
    }

    public function actionFill() {
//        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/category/delete', 'cat_0', 'POST');
//        Yii::dump(__METHOD__, $r);
//        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/category/delete', 'cat_1', 'POST');
//        Yii::dump(__METHOD__, $r);
//        return;
        for ($i = 0; $i < 10; $i++) {
            $id = uniqid('test-id_');
            $brand_id = mt_rand(0, 2);
            $cat_id = 'cat_' . mt_rand(0, 1);
            $data = [
                'id' => $id,
                'title' => "Тестовый товар {$i}",
                'article' => "Артикул {$i}",
                'code' => "code-{$i}",
                'variant_bar_codes' => [['variant_id' => "var-{$i}", 'barcode' => "112233-{$i}", 'main' => 0]],
                'price' => mt_rand(100, 999),
                'descr' => "описание {$i}",
                'qty_pack' => 1,
                'weight' => 2,
                'brand' => "ТестБренд {$brand_id}",
                'brand_id' => $brand_id,
                'category' => "Тест категория {$cat_id}",
                'category_id' => $cat_id,
                'pic' => '1000',
                'params' => [],
                'photos' => [
                ],
                'variants' => [['variant_id' => "var-{$i}", 'color' => "тест цвет {$i}", 'size' => "тест размер {$i}", 'qty' => mt_rand(1, 10), 'qty_alt' => mt_rand(1, 10), 'is_new' => $i === 0]],
                'prices' => [['price_type_id' => 1, 'price' => 99]],
                'discount_group_id' => 1
            ];
            for ($p = 0; $p < 10; $p++) {
                $pt = $p % 2 === 0 ? "Параметр {$p}" : "Тестовый параметр {$p}";
                $data['params'][] = ['id' => "pid{$p}", 'title' => $pt, 'unit' => 'шт.', 'value' => mt_rand(1, 100), 'type_id' => Params::TYPE_GOODS_AND_FILTER];
            }
            $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/goods/save', $data, 'POST');
            Yii::dump(__METHOD__, $r);
        }
//        $data = $id;
//        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/goods/delete', $data, 'POST');
//        Yii::dump(__METHOD__, $r);
    }

    public function actionGoods() {
//        $id = 'test-id-1s';
//        $data = [
//            'id' => $id,
//            'title' => 'Тестовый товар',
//            'article' => 'Артикул 1',
//            'code' => '12345',
//            'variant_bar_codes' => [['variant_id' => $id, 'barcode' => '112233', 'main' => 0]],
//            'price' => 345.67,
//            'descr' => 'описание',
//            'qty_pack' => 1,
//            'weight' => 2,
//            'brand' => 'ТестБренд',
//            'brand_id' => $id,
//            'category' => 'Тест категория',
//            'category_id' => $id,
//            'pic' => '1000',
//            'params' => [['id' => $id, 'title' => 'Тестовый параметр', 'unit' => 'шт.', 'value' => 12, 'type_id' => Params::TYPE_GOODS_AND_FILTER]],
//            'photos' => [
//                ['id' => $id . '-2', 'param_id' => null, 'ext' => 'jpg', 'data' => base64_encode(file_get_contents(Yii::getAlias('@app/web/files/images/krym.jpg'))), 'main' => 0, 'variant_id' => null],
//                ['id' => $id, 'param_id' => null, 'ext' => 'jpg', 'data' => base64_encode(file_get_contents(Yii::getAlias('@app/web/files/images/watermark.jpg'))), 'main' => 1, 'variant_id' => null],
//            ],
//            'variants' => [['variant_id' => $id, 'color' => 'тест цвет', 'size' => 'тест размер', 'qty' => 100, 'qty_alt' => 10, 'is_new' => 0]],
//            'prices' => [['price_type_id' => '000000001', 'price' => 123]],
//            'discount_group_id' => 1
//        ];
        $data = include(Yii::$app->getRuntimePath() . '/api_data/api/goods/save/00000013850.php');
        $data['photos'] = [];
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/goods/save', $data, 'POST');
        //$r = Api::request('https://ukrmedias.dev3.ingsot.com/api/system/test', $data, 'POST');
        Yii::dump(__METHOD__, $r);
//        $data = $id;
//        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/goods/delete', $data, 'POST');
//        Yii::dump(__METHOD__, $r);
    }

    public function actionStock() {
        $id = 'test-id-1s';
        $data = [
            'stock' => [['id' => $id, 'variant_id' => $id, 'qty' => 99, 'qty_alt' => 9, 'price' => 99]],
            'prices' => [['id' => $id, 'price_type_id' => 2, 'price' => 57]],
        ];
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/goods/stock', $data, 'POST');
        Yii::dump(__METHOD__, $r);
    }

    public function actionPrices() {
        $id = 'test-id-1s';
        $data = ['id' => $id, 'title' => 'Test Price type'];
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/prices/save', $data, 'POST');
        Yii::dump(__METHOD__, $r);
    }

    public function actionPromo() {
        $id = 'test-id-1s';
        $data = [
            'id' => $id,
            'date_from' => '2021-01-01',
            'date_to' => '2021-12-31',
            'goods' => [
                ['id' => $id, 'discount' => 50],
                ['id' => '1', 'discount' => 22],
            ]
        ];
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/promo/save', $data, 'POST');
        Yii::dump(__METHOD__, $r);
//        $data = $id;
//        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/promo/delete', $data, 'POST');
//        Yii::dump(__METHOD__, $r);
    }

    public function actionCurr() {
        $data = ['date' => '2021-02-02', 'currency' => 'USD', 'rate' => '11'];
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/currency/save', $data, 'POST');
        Yii::dump(__METHOD__, $r);
    }

    public function actionOrder() {
        $id = 'test-id-1s';
        $data = [
            'id' => $id,
            'number' => '00000test',
            'request_id' => 2,
            'status_id' => '',
            'delivery_id' => null,
            'date' => date('Y-m-d'),
            'amount' => 10000,
            'manager_note' => 'manager_note',
            'firm_id' => '000003800',
            'details' => [
                [
                    'id' => $id,
                    'variant_id' => $id,
                    'qty' => 3,
                    'price' => 100,
                    'discount' => 43,
                    'title' => 'DelTitle',
                    'brand' => 'DelBrand',
                    'code' => 'code-1',
                    'article' => 'a-1',
                    'bar_code' => 'bar-1',
                    'color' => 'Красный',
                    'size' => '72',
                ],
                [
                    'id' => '',
                    'variant_id' => '',
                    'qty' => 3,
                    'price' => 100,
                    'discount' => 43,
                    'title' => 'DelTitle',
                    'brand' => 'DelBrand',
                    'code' => 'code-1',
                    'article' => '',
                    'bar_code' => '',
                    'color' => '',
                    'size' => '',
                ]
            ],
        ];
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/orders/save', $data, 'POST');
        Yii::dump(__METHOD__, $r);
//        $data = $id;
//        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/orders/delete', $data, 'POST');
//        Yii::dump(__METHOD__, $r);
    }

    public function actionStatus() {
        $id = 'test-id-1s';
        $data = [
            'id' => $id,
            'status_id' => Orders::STATUS_READY,
        ];
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/orders/status', $data, 'POST');
        Yii::dump(__METHOD__, $r);
    }

    public function actionExport() {
        #Export::preorders();
        #Export::firms();
    }

    public function actionDelete() {
        $data = 'test-id-1s';
        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/prices/delete', $data, 'POST');
        Yii::dump(__METHOD__, $r);
//        $data = $id;
//        $r = Api::request(Yii::$app->params['apiTestUrl'] . '/api/orders/delete', $data, 'POST');
//        Yii::dump(__METHOD__, $r);
    }

}
