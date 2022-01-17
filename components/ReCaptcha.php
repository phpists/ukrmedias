<?php

namespace app\components;

use \Yii;
use yii\web\View;

class ReCaptcha {

    static public function isValid() {
        return true;
//        if (Yii::$app->user->isGuest === false) {
//            return true;
//        }
//        return Yii::$app->request->isPost && self::validate();
    }

    static protected function validate() {
        include(Yii::getAlias('@app/components/recaptcha_v3/autoload.php'));
        $recaptcha = new \ReCaptcha\ReCaptcha(Yii::$app->params['recaptcha-server-key']);
        $resp = $recaptcha->setExpectedHostname(getenv('HTTP_HOST'))
                #->setExpectedAction(Yii::app()->getController()->getRoute())
                #->setScoreThreshold(0.5)
                ->verify(Yii::$app->request->post('g-recaptcha-response'), getenv('REMOTE_ADDR'));
        #Yii::dumpAlt($_POST, 'recaptcha');
        #Yii::dumpAlt($resp->toArray(), 'recaptcha');
        return $resp->isSuccess();
    }

    static public function field($isAjaxForm = false) {
        #if (Yii::$app->user->isGuest === false) {
            return;
        #}
        $key=Yii::$app->params['recaptcha-site-key'];
        $lang=Yii::$app->language;
        $id = uniqid('ReCaptcha');
        //Yii::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js?render=' . Yii::$app->params['recaptcha-site-key'] . '&hl=' . Yii::$app->language, ['position' => View::POS_END]);
        $mainPart = "grecaptcha.execute('{$key}').then(function(token) { document.getElementById('{$id}').value=token; });";
        $ajaxPart = $isAjaxForm ? "document.getElementById('{$id}').closest('form').addEventListener('submit',function(){ grecaptcha.execute('{$key}').then(function(token) { document.getElementById('{$id}').value=token; }); });" : '';
        $s = "var s = document.createElement('script');s.src='https://www.google.com/recaptcha/api.js?render={$key}&hl={$lang}';s.onload=function(){grecaptcha.ready(function() {{$mainPart}{$ajaxPart}});};document.getElementsByTagName('head')[0].appendChild(s);";
        Yii::$app->view->registerJs("setTimeout(function(){{$s}},200);", View::POS_END, $id);
        return '<input id="' . $id . '" type="hidden" name="g-recaptcha-response"/>';
    }

}
