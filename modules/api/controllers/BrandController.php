<?php

namespace app\modules\api\controllers;

use \Yii;
use app\components\Misc;
use app\models\Api;
use app\models\Brands;
use app\models\Goods;

class BrandController extends A_Controller {

    public function actionDelete() {
        $res = Yii::transaction(function () {
                    $model = Brands::findOne(Api::$data);
                    if ($model === null) {
                        return true;
                    }
                    $this->logName = $model->title;
                    Misc::iterator(Goods::find()->where(['brand_id' => $model->id]), function ($goods) {
                        $goods->deleteModel();
                    });
                    return $model->deleteModel();
                });
        return ['res' => $res, 'message' => ''];
    }

}
