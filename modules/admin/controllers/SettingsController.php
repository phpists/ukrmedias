<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\filters\AccessControl;
use app\components\AdminController;
use app\components\Auth;
use app\models\Settings;

class SettingsController extends AdminController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => [Auth::ROLE_ADMIN],
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
    public function actionIndex() {
        $model = new Settings();
        $model->initData();
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            $res = $model->saveModel();
            $res ? $t->commit() : $t->rollBack();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                return $this->refresh();
            }
        }
        return $this->render('index', [
                    'model' => $model,
        ]);
    }

}
