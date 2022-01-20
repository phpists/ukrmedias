<?php

namespace app\modules\client\controllers;

use yii\helpers\Url;
use \Yii;
use app\components\ClientController;
use app\components\Misc;
use app\models\Cpu;
use app\models\Category;
use app\models\Goods;
use app\models\Brands;
use app\models\GoodsFilter;
use app\models\Params;
use app\models\DataHelper;
use app\models\PreOrders;
use app\models\Promo;

class CatalogController extends ClientController {

    public $layout = '@app/modules/client/views/layouts/catalog.php';
    public $enableCsrfValidation = false;
    public $brandModel;


    public function actionIndex($cpu, $brandCpu = null) {

        $this->categoryModel = $model = Cpu::findBy($cpu);
        if ($brandCpu !== null) {
            $this->brandModel = Cpu::findBy($brandCpu);
            if (!$this->brandModel instanceof Brands) {
                throw new \yii\web\HttpException(404);
            }
        }

        $facets = Goods::fasetsByBrands($_POST['b'] ?? [], $model->id);

        switch (get_class($model)):
            case 'app\models\Category':
                if ($model->isLeaf()) {
                    $filter = new GoodsFilter($model, $brandCpu === null ? null : $this->brandModel->id);
                    if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
                        $html = $this->renderPartial('_filter_result', [
                                    'model' => $model,
                                    'filter' => $filter,
                        ]);

                        return $this->asJson([
                            'html' => $html,
                            'facets' => $facets
                        ]);
                    }
                    $categoryData = DataHelper::getCategoryData($model->id);
                    $html = $this->render('category_leaf', [
                        'model' => $model,
                        'filter' => $filter,
                        'brands' => $brandCpu === null ? $categoryData['brands'] : [],
                        'params' => $categoryData['params'],
                        'brandCpu' => $brandCpu,
                        'cats' => [],
                    ]);
                } else {
                    GoodsFilter::resetStateDirect();
                    $html = $this->render('category', [
                        'model' => $model,
                        'cats' => $brandCpu === null ? $model->findChildren() : $this->brandModel->findCategories($this->categoryModel),
                        'brandCpu' => $brandCpu,
                        'brand_id' => $brandCpu === null ? null : $this->brandModel->id,
                    ]);
                }
                break;
            case 'app\models\Goods':
                $this->categoryModel = $model->getCategory();
                $html = $this->render('goods', [
                    'category' => Category::findOne($model->cat_id),
                    'model' => $model,
                    'variantsGrouped' => $model->getVariantsGrouped(),
                    'goodsQty' => PreOrders::getGoodsQty(),
                ]);
                break;
            case 'app\models\Brands':
                GoodsFilter::resetStateDirect();
                $html = $this->render('brand', [
                    'model' => $model,
                    'cats' => $model->findCategories(Category::findOne(Category::ROOT_ID)),
                    'brandCpu' => $cpu,
                ]);
                break;
            default:
                throw new \yii\web\HttpException(404);
        endswitch;
        return $html;
    }

    public function actionSearch($q) {
        if ($q == '') {
            $ref = getenv('HTTP_REFERER');
            if ($ref == '') {
                return $this->redirect(['/client/profile/dashboard']);
            } else {
                return $ref;
            }
        }
        $model = Category::findOne(Category::ROOT_ID);
        $filter = new GoodsFilter($model, null, $q);
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            return $this->renderPartial('_filter_result', [
                        'model' => $model,
                        'filter' => $filter,
            ]);
        }
        return $this->render('category_leaf', [
                    'model' => $model,
                    'filter' => $filter,
                    'brands' => Brands::keyval(),
                    'params' => [],
                    'brand_id' => null,
                    'cats' => [],
        ]);
    }

    public function actionNovelty() {
        $model = Category::findOne(Category::ROOT_ID);
        $filter = new GoodsFilter($model, null, null, false, true);
        $model->title = 'Новинки';
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            return $this->renderPartial('_filter_result', [
                        'model' => $model,
                        'filter' => $filter,
            ]);
        }
        $categoryData = DataHelper::getCategoryData($model->id);
        return $this->render('category_leaf', [
                    'model' => $model,
                    'filter' => $filter,
                    'brands' => $categoryData['brandsNovelty'],
                    'params' => [],
                    'brand_id' => null,
                    'cats' => $categoryData['catsNovelty'],
        ]);
    }

    public function actionPromo($id = null) {
        if ($id !== null) {
            $promo = Promo::findOneActual($id);
            if ($promo === null) {
                return $this->redirect(['/client/profile/dashboard']);
            }
        }
        $model = Category::findOne(Category::ROOT_ID);
        $filter = new GoodsFilter($model, null, null, true, false);
        $model->title = 'Акційні товари';
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            return $this->renderPartial('_filter_result', [
                        'model' => $model,
                        'filter' => $filter,
            ]);
        }
        $promoData = DataHelper::getPromoData($id);
        return $this->render('category_leaf', [
                    'model' => $model,
                    'filter' => $filter,
                    'brands' => $promoData['brands'],
                    'params' => [],
                    'brand_id' => null,
                    'cats' => $promoData['cats'],
        ]);
    }

}
