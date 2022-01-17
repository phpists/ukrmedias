<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\ClientController;
use app\models\Feedback;

class FeedbackController extends ClientController {

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionIndex() {
        $model = new Feedback();
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->saveByClient(Yii::$app->user->identity->getFirm());
            if ($res) {
                Yii::$app->session->setFlash('success', 'Ваше звернення прийнято.');
                return $this->refresh();
            }
        }
        return $this->render('index', [
                    'model' => $model,
        ]);
    }

}
