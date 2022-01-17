<?php

namespace app\components;

use yii\filters\AccessControl;
use \Yii;
use app\models\Category;

class ClientController extends BaseController {

    public $cats;
    public $categoryModel;

    public function init() {
        parent::init();
        $this->cats = Category::findOne(Category::ROOT_ID)->findList();
    }

    public function beforeAction($action) {
        #if (Yii::$app->user->identity->is_auto && $this->route !== 'client/profile/registration') {
        #    $this->redirect('/client/profile/registration');
        if (Yii::$app->user->can(Auth::ROLE_CLIENT_TEST) && Yii::$app->request->isPost) {
            if (!Yii::$app->request->isAjax) {
                Yii::$app->session->setFlash('success', 'Демонстраційна версія.');
                $this->redirect(getenv('HTTP_REFERER'));
            } elseif (!preg_match('@^client/(catalog|cart/index|cart/add|cart/del)@', $this->route)) {
                Yii::$app->end();
            }
        }
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => [Auth::ROLE_CLIENT_TEST, Auth::ROLE_CLIENT_DEFAULT],
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

}
