<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;
use app\models\Category;

class CategoryController extends A_Controller {

    public function actionDelete() {
        $res = Yii::transaction(function () {
                    $model = Category::find()->where(['id_1s' => Api::$data])->one();
                    if ($model === null) {
                        return true;
                    }
                    $this->logName = $model->title . '-' . $model->id;
                    return $model->deleteModel();
                });
        return ['res' => $res, 'message' => ''];
    }

}
