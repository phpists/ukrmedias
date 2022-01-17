<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\filters\AccessControl;
use app\components\AdminController;
use app\components\Auth;
use app\models\Factory;
use app\models\Services;

class TestController extends AdminController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => [Auth::ROLE_SUPERADMIN],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex($id = null) {
        if (!array_key_exists($id, Services::$labels)) {
            $id = Services::ID_COOL_WATER;
        }
        Factory::setCalculatorModel($id);
        $calculator = Factory::getCalculator();
        $dataset = Factory::getCalculator()->getDataset();
        foreach ($dataset as $house) {
            Factory::getCalculator()->preview($house, date('Y-m-01'), date('Y-m-t'), $id);
        }
        return $this->render('index', [
                    'service_id' => $id,
                    'calculator' => $calculator,
                    'dataset' => $dataset,
        ]);
    }

    public function actionTest7() {
        Factory::setCalculatorModel('\app\models\CalculatorTest7');
        $calculator = Factory::getCalculator();
        $house = Factory::getCalculator()->getDataset();
        Factory::getCalculator()->preview($house, date('Y-m-01'), date('Y-m-t'), Services::ID_HEAT);
        return $this->render('test7', [
                    'service_id' => null,
                    'calculator' => $calculator,
                    'house' => $house,
        ]);
    }

}
