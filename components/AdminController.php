<?php

namespace app\components;

use yii\filters\AccessControl;
use \Yii;

class AdminController extends BaseController {

    public function init() {
        parent::init();
        $this->layout = '@app/modules/admin/views/layouts/main.php';
    }

//    public function beforeAction($action) {
//        if (Yii::$app->user->can(Auth::ROLE_DEMO) && Yii::$app->request->isPost) {
//            Yii::$app->session->setFlash('success', 'Демонстраційна версія.');
//            if (!Yii::$app->request->isAjax) {
//                $this->redirect(getenv('HTTP_REFERER'));
//            }
//            return false;
//        }
//        return parent::beforeAction($action);
//    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => [Auth::ROLE_MANAGER],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        //save Grid Filter Params
        $key = 'grid_filter_params_' . $this->getRoute();
        if (Yii::$app->request->isAjax && !empty($_GET) && isset($_GET['_pjax'])) {
            $get = $_GET;
            unset($get['id']);
            Yii::$app->session->set($key, $get);
        } elseif (Yii::$app->request->isAjax === false && empty($_GET) && Yii::$app->session->has($key)) {
            $_GET = Yii::$app->session->get($key);
        }
        return parent::beforeAction($action);
    }

}
