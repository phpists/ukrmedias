<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\filters\AccessControl;
use app\components\AdminController;
use app\components\Auth;
use app\components\Misc;
use app\models\Clients;
use app\models\Api;
use app\models\Services;

class ApiController extends AdminController {

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
    public function actionIndex() {
        Api::$client = Clients::find()->one();
        Api::$client->services = [Services::ID_COOL_WATER];
        Api::$client->api = 1;
        return $this->render('index');
    }

    public function actionDownload() {
        Api::$client = Clients::find()->one();
        Api::$client->services = [Services::ID_COOL_WATER];
        Api::$client->api = 1;
        $file = Yii::$app->getRuntimePath() . '/apidoc.html';
        if (!is_file($file)) {
            file_put_contents($file, '<style>' . file_get_contents(__DIR__ . '/../views/api/doc.css') . '</style>' . $this->renderFile("@app/modules/client/views/api/doc.php"));
            exec("google-chrome-stable --headless --disable-gpu --no-sandbox --print-to-pdf={$file}.pdf {$file} >/dev/null");
        }
        return Misc::sendFile("{$file}.pdf", Yii::$app->name . " - документація по API.pdf", false);
    }

}
