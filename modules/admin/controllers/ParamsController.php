<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use yii\filters\AccessControl;
use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\models\Params;

class ParamsController extends AdminController {

    public function actionIndex() {
        $model = new Params;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
                    'route' => ['index'],
        ]);
    }

    public function actionUpdatePos() {
        Params::updatePos((array) Yii::$app->request->post('ids'));
    }

}
