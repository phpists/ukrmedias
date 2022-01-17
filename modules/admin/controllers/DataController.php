<?php

namespace app\modules\admin\controllers;

use yii\helpers\Html;
use \Yii;
use yii\filters\AccessControl;
use app\components\AdminController;
use app\components\Auth;
//use app\components\Misc;
use app\models\Tasks;
use app\models\ModelFiles;
use app\models\novaposhta\NP_CitiesNp;
use app\models\novaposhta\NP_Offices;

class DataController extends AdminController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => [Auth::ROLE_MANAGER, Auth::ROLE_CLIENT_TEST, Auth::ROLE_CLIENT_DEFAULT],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    public function actionProgress($id) {
        return Tasks::getProgress($id);
    }

    public function actionModelFileUpload($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $file = ModelFiles::upload($id);
        $this->layout = false;
        return array(
            'mess' => $file->hasErrors() ? Html::errorSummary($file, ['header' => '']) : '',
            'link' => $file->hasErrors() ? '' : $file->getAdminIconItem(),
        );
    }

    public function actionModelFileDelete($id) {
        if (Yii::$app->request->isPost) {
            ModelFiles::del($id);
            return '1';
        }
    }

    public function actionModelFileSort() {
        ModelFiles::updatePos((array) Yii::$app->request->post('ids'));
    }

    public function actionModelFileDownload($id) {
        $file = ModelFiles::download($id);
        if ($file->hasErrors()) {
            Yii::$app->session->setFlash('error', Html::errorSummary($file, ['header' => '']));
            if (getenv('HTTP_REFERER') <> '') {
                $this->redirect(getenv('HTTP_REFERER'));
            } else {
                $this->redirect('index');
            }
        }
    }

    public function actionCitiesOptions($id) {
        $options = ['prompt' => ''];
        return Html::renderSelectOptions('', NP_CitiesNp::keyval($id), $options);
    }

    public function actionOfficesOptions($id) {
        $data = NP_Offices::keyval($id);
        if (count($data) > 1) {
            $selected = '';
            $options = ['prompt' => ''];
        } else {
            $options = [];
            $selected = key($data);
        }
        return Html::renderSelectOptions($selected, $data, $options);
    }

}
