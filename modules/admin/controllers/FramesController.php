<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\AdminController;
use app\models\Frames;

class FramesController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $model = new Frames();
        $dp = $model->search();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dp,
        ]);
    }

}
