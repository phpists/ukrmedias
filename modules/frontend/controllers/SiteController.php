<?php

namespace app\modules\frontend\controllers;

use yii\helpers\Url;
use \Yii;
use app\components\Misc;
use app\components\BaseController;
use app\components\Auth;
use app\components\AutoLogin;
use app\models\Users;
use app\models\Firms;
use app\models\Letters;
use app\models\AccessLogic;
use app\components\TurboSms;

class SiteController extends BaseController {

    public $layout = '@app/modules/frontend/views/layouts/mini.php';

    public function beforeAction($action) {
        if ($action->id === 'login') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionAutologin() {
        $model = Users::findOne(AutoLogin::getUserId());
        if ($model) {
            $model->autoLogin();
            return $this->redirect(Auth::defineHomeUrl());
        } else {
            AutoLogin::delCookie();
        }
        return $this->redirect(['index']);
    }

    public function actionIndex() {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Auth::defineHomeUrl());
        }
        if (AutoLogin::getUserId()) {
            return $this->redirect(['autologin']);
        }
        $model = new Users();
        $model->setScenario('auth');
        if ($model->load(Yii::$app->request->post())) {
            $data = [0 => 'login', 'sid' => (string) mt_rand(1000, 9999)];
            $password = Misc::oneTimePassword();
            if (strpos($model->email, '@') === false) {
                $phone = Misc::normalizePhone($model->email);
                $user_id = Users::find()->select('id')->where(['phone' => $phone, 'active' => 1])->scalar();
                $data['data'] = $phone;
                if ($user_id) {
                    TurboSms::send($phone, "Ваш пароль (для сесії №{$data['sid']}):\n{$password}");
                } else {
                    return $this->redirect('registration-step1');
                }
            } else {
                $user_id = Users::find()->select('id')->where(['email' => $model->email, 'active' => 1])->scalar();
                $data['data'] = $model->email;
                if ($user_id) {
                    Letters::setupTask([
                        'to' => $model->email,
                        'subject' => 'Пароль для входу на сайт' . Yii::$app->name,
                        'body' => $this->renderPartial('_password', ['sid' => $data['sid'], 'password' => $password]),
                    ]);
                } else {
                    return $this->redirect('registration-step1');
                }
            }
            if ($user_id) {
                Yii::$app->session->set('password', $password);
                Yii::$app->session->set('user_id', $user_id);
                Yii::$app->session->set('sid', $data['sid']);
            }
            return $this->redirect(Url::to($data));
        }
        return $this->render('start', [
                    'model' => $model,
        ]);
    }

    public function actionLogin($sid = null, $data = null) {
        if ($data === null || $sid === null) {
            return $this->redirect(['index']);
        }
        if (Yii::$app->session->get('password', 1) === Yii::$app->request->post('password', 2) && $sid === Yii::$app->session->get('sid', 0)) {
            AutoLogin::setCookie(Yii::$app->session->get('user_id'));
            Yii::$app->session->set('password', null);
            Yii::$app->session->set('user_id', null);
            Yii::$app->session->set('qty', null);
            Yii::$app->session->set('sid', null);
            return $this->redirect(['autologin']);
        }
        if (Yii::$app->request->isPost) {
            $qty = (int) Yii::$app->session->get('qty', 0);
            $qty++;
            if ($qty < 3) {
                Yii::$app->session->set('qty', $qty);
                return $this->refresh();
            } else {
                Yii::$app->session->set('password', null);
                Yii::$app->session->set('user_id', null);
                Yii::$app->session->set('qty', null);
                return $this->redirect(['index']);
            }
        }
        return $this->render('login', [
                    'sid' => $sid,
                    'data' => $data,
        ]);
    }

    public function actionLogin2() {
        $model = new Users();
        $model->setScenario('auth');
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(Auth::defineHomeUrl());
        }
        $model->password = '';
        return $this->render('login2', [
                    'model' => $model,
        ]);
    }

    public function actionRegistrationStep1() {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Auth::defineHomeUrl());
        }

        $model = new Firms(['scenario' => 'registration']);
        $model->setAttributes((array) Yii::$app->session['Firm']);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->session['Firm'] = $model->getAttributes(['title', 'phone']);
            return $this->redirect(['registration-step2']);
        }

        return $this->render('registration_step1', [
                    'model' => $model,
        ]);
    }

    public function actionRegistrationStep2() {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Auth::defineHomeUrl());
        }
        $firmAttrs = Yii::$app->session['Firm'];
        if (!is_array($firmAttrs)) {
            return $this->redirect(['registration-step1']);
        }

        $firm = new Firms(['scenario' => 'registration']);
        $firm->setAttributes($firmAttrs);
        $model = new Users(['scenario' => 'registration']);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::transaction(function ()use ($firm, $model) {
                $firm->register();
                $model->register($firm->id);
                return true;
            });
            return $this->redirect(['index']);
        }
        return $this->render('registration_step2', [
                    'model' => $model,
        ]);
    }

    public function actionRestore() {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Auth::defineHomeUrl());
        }

        $model = new Users();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->restore()) {
                $url = Url::toRoute(['/frontend/site/password', 'id' => $model->id, 'key' => $model->key], true);
                Letters::setupTask([
                    'to' => $model->email,
                    'subject' => 'Відновлення паролю на сайті ' . Yii::$app->name,
                    'body' => $this->renderPartial('_letter_restore', ['url' => $url]),
                ]);
                Yii::$app->session->setFlash('success', 'Інструкція щодо відновлення паролю відправлена Вам на e-mail.');
            } else {
                Yii::$app->session->setFlash('error', 'Відновлення паролю для вказаного користувача неможливо. Спробуйте звернутися із запитом через сторінку "Контакти".');
            }
            return $this->redirect(['/frontend/site/login']);
        }
        return $this->render('restore', [
                    'model' => $model,
        ]);
    }

    public function actionPassword($key = null, $id = null) {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Auth::defineHomeUrl());
        }
        if (empty($key) || empty($id)) {
            return $this->redirect(['/frontend/site/login']);
        }
        $model = Users::findByKey($id, $key);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Відновлення паролю для вказаного користувача неможливо.');
            return $this->redirect(['/frontend/site/login']);
        }
        if ($model->load(Yii::$app->request->post()) && !empty($model->password_new1)) {
            $model->createPassword();
            if ($model->autoLogin()) {
                AutoLogin::setCookie(Yii::$app->user->getId());
                Yii::$app->session->setFlash('success', 'Новий пароль активований.');
                return $this->redirect(Auth::defineHomeUrl());
            } else {
                throw new \yii\base\ErrorException('Помилка автоматичного входу в систему.', 403);
            }
        }
        $model->password_new1 = Yii::$app->security->generateRandomString(16);
        return $this->render('password', [
                    'model' => $model,
        ]);
    }

    public function actionActivate($key = null, $id = null, $uid = null) {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Auth::defineHomeUrl());
        }
        if (empty($key) || empty($id)) {
            return $this->redirect(['/frontend/site/login']);
        }

        $model = Users::findByKey($id, $key);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Активація облікового запису неможлива. Спробуйте скористатися функцією відновлення паролю.');
            return $this->redirect(['/frontend/site/login']);
        }
        if ($model->load(Yii::$app->request->post()) && !empty($model->password_new1)) {
            $model->createPassword();
            if ($uid > 0) {
                $this->notifyOwner($model, $uid);
            }
            if ($model->autoLogin()) {
                AutoLogin::setCookie(Yii::$app->user->getId());
                Yii::$app->session->setFlash('success', 'Ваш обліковий запис активований.');
                return $this->redirect(Auth::defineHomeUrl());
            } else {
                throw new \yii\base\ErrorException('Помилка автоматичного входу в систему.', 403);
            }
        }
        $model->password_new1 = Yii::$app->security->generateRandomString(16);
        return $this->render('password', [
                    'model' => $model,
        ]);
    }

    protected function notifyOwner($user, $id) {
        $owner = Users::findOne($id);
        if ($owner === null || !$owner->active || !$owner->email) {
            return;
        }
        Letters::setupTask([
            'to' => $owner->email,
            'subject' => 'Активація облікового запису',
            'body' => Auth::$rolesInternal[$user->role_id] . ' ' . $user->getName() . ' активував свій обліковий запис.',
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout() {
        if (AccessLogic::isLoginAs()) {
            $data = AccessLogic::get();
            AccessLogic::resetLoginAs();
            $user = Users::findOne($data['id']);
            if ($user instanceof Users) {
                Yii::$app->user->logout(false);
                AutoLogin::setCookie($user->id);
                $user->autoLogin();
                return $this->redirect($data['url']);
            }
        }
        Yii::$app->user->logout();
        AutoLogin::delCookie();
        return $this->redirect(['index']);
    }

    public function actionError() {
        $ex = Yii::$app->errorHandler->exception;
        $code = property_exists($ex, 'statusCode') ? $ex->statusCode : $ex->getCode();
//        if ($code == 0) {
//            Yii::dump($ex->getMessage());
//            #Yii::dump($_GET);
//            #Yii::dump($_POST);
//            //Yii::dump($_SERVER);
//        }
        return $this->render('error', [
                    'code' => $code,
                    'message' => YII_DEBUG ? $ex->getMessage() : Misc::internalErrorMessage()
        ]);
    }

}
