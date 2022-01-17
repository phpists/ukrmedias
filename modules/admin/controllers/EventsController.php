<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\models\Events;

class EventsController extends AdminController {

    public function actionIndex() {
        $model = new Events;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
        ]);
    }

}
