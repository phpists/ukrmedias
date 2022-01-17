<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\ClientController;
use app\models\Feedback;
use app\models\Letters;
use app\models\Firms;
use app\models\Users;

class RequestController extends ClientController {

    public function actionIndex() {
        $model = new Feedback();
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            $firm = Firms::findOne(Yii::$app->user->identity->client_id);
            $res = $model->saveByClient($firm);
            if ($res) {
                foreach (Users::getAdminEmails($firm->id) as $email) {
                    Letters::setupTask([
                        'to' => $email,
                        'subject' => "Звернення Клієнта {$firm->getTitle()}",
                        'body' => $model->getMess(),
                    ]);
                }
            }
            $res ? $t->commit() : $t->rollBack();
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
