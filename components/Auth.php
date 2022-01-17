<?php

namespace app\components;

use \Yii;

class Auth {

    const ROLE_DEVELOPER = '000';
    const ROLE_SUPERADMIN = 'm01';
    const ROLE_ADMIN = 'm02';
    const ROLE_MANAGER = 'm03';
    const ROLE_CLIENT_TEST = 'c00';
    const ROLE_CLIENT_DEFAULT = 'c01';

    static public $rolesInternal = [
        self::ROLE_MANAGER => 'менеджер',
        self::ROLE_ADMIN => 'адміністратор',
        self::ROLE_SUPERADMIN => 'суперадміністратор',
        self::ROLE_CLIENT_TEST => 'клієнт DEMO',
        self::ROLE_CLIENT_DEFAULT => 'клієнт',
    ];
    static public $roleLabelsOwner = [
        self::ROLE_MANAGER => 'менеджер',
        self::ROLE_ADMIN => 'адміністратор',
        self::ROLE_SUPERADMIN => 'суперадміністратор',
    ];
    static public $roleLabelsClient = [
        self::ROLE_CLIENT_DEFAULT => 'клієнт',
        self::ROLE_CLIENT_TEST => 'клієнт DEMO',
    ];

    static public function defineHomeUrl() {
        $role_id = Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->role_id;
        $isAuto = Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->is_auto;
        switch ($role_id):
            case self::ROLE_DEVELOPER:
            case self::ROLE_SUPERADMIN:
            case self::ROLE_ADMIN:
            case self::ROLE_MANAGER:
                $url = '/admin/feedback/index';
                break;
            case self::ROLE_CLIENT_DEFAULT:
            case self::ROLE_CLIENT_TEST:
                if ($isAuto) {
                    $url = '/client/profile/registration';
                } else {
                    $url = '/client/profile/dashboard';
                }
                break;
            default:
                $url = '/frontend/site/logout';
        endswitch;
        return $url;
    }

    static public function isManager($role_id = null) {
        if ($role_id === null) {
            $role_id = Yii::$app->user->identity->role_id;
        }
        return strpos($role_id, 'm') === 0 || $role_id == self::ROLE_DEVELOPER;
    }

    static public function isClient($role_id = null) {
        if ($role_id === null) {
            $role_id = Yii::$app->user->identity->role_id;
        }
        return strpos($role_id, 'c') === 0;
    }

    static public function isAbonent($role_id = null) {
        if ($role_id === null) {
            $role_id = Yii::$app->user->identity->role_id;
        }
        return strpos($role_id, 'a') === 0;
    }

}
