<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use \Yii;
use app\components\Misc;
use app\components\ModelsIterator;
use app\models\novaposhta\NovaPoshta;
use app\models\Firms;
use app\models\Brands;
use app\models\Goods;
use app\models\Category;
use app\models\DataHelper;
use app\models\Params;
use app\models\PreOrdersDetails;
use app\models\Cpu;
use app\models\Promo;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DevController extends Controller {

    public function actionTmp() {
//        Misc::iterator(Category::find(), function ($category) {
//            DataHelper::setCategoryData($category);
//        });
//        Misc::iterator(Promo::find(), function ($model) {
//            DataHelper::setPromoData($model->id);
//        });
//        DataHelper::setPromoData(null);
//        Misc::iterator(Cpu::find(), function ($model) {
//            if (!$model->findOwner()) {
//                $model->delete();
//            }
//        });
//        Misc::iterator(Firms::find(), function ($model) {
//            $model->addNewUsers();
//        });
        Misc::iterator(Goods::find(), function ($model) {
            $model->getCpuModel()->setScenario('import');
            $model->updateModel();
        });
        //DataHelper::setPromoGoods();
        #NovaPoshta::syncCitiesBook();
        #NovaPoshta::syncOfficesBook();
        #NovaPoshta::syncMessages();
        #NovaPoshta::syncTypes();
        #NovaPoshta::syncPaymentTypes();
        #NovaPoshta::syncPayerTypes();
        #NovaPoshta::syncPayerTypesBack();
        #NovaPoshta::syncDeliveryTypes();
        #NovaPoshta::syncCargoTypes();
        #NovaPoshta::syncCargoDescr();
        #NovaPoshta::syncSenders();
    }

    public function actionTask() {
        $i = new ModelsIterator([
//          'query' => Goods::find()->where(['cat_id' => 43, 'brand_id' => '000000324']),
            'query' => Goods::find(),
            'beforeOffset' => function ($i) {
                Yii::dumpAlt("offset={$i->offset}", 'photos_resize');
            },
            'callback' => function ($item) {
                foreach ($item->getModelFiles('photos')->findMulti() as $dataModel) {
                    $dataModel->resizeSelf();
                    foreach (\yii\helpers\FileHelper::findFiles(dirname($dataModel->getFile()), ['only' => ['*.webp']]) as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                }
            },
        ]);
        Yii::dumpAlt('start', 'photos_resize', false);
        $i->run();
        Yii::dumpAlt('finish', 'photos_resize');
    }

}
