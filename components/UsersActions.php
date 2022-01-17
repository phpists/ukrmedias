<?php

namespace app\components;

use \Yii;
use app\models\AccessLogic;
use app\models\DataHelper;

class UsersActions {

    static protected $tableName = 'users_actions';

    static public function add() {
        if (Yii::$app->user->isGuest || Yii::$app->request->isGet === true || preg_match('@^(api|frontend/site|(admin|client|abonent)/profile/password)/@', Yii::$app->controller->route)) {
            return;
        }
        $post = Yii::$app->request->post();
        unset($post['_csrf']);
        if (isset($post['data'])) {
            $data = json_decode(Yii::$app->security->validateData(base64_decode($post['data']), DataHelper::HASH_KEY), true);
            if ($data) {
                $post['data'] = $data;
            }
        }
        Yii::$app->db->createCommand()->insert(self::$tableName, [
            'user_id' => Yii::$app->user->getId(),
            'real_user_id' => AccessLogic::get('id'),
            'request_uri' => Yii::$app->request->getUrl() . '; route=' . Yii::$app->controller->route,
            'post' => var_export($post, true),
        ])->execute();
    }

}
