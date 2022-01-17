<?php

namespace app\components;

use Yii;
use yii\web\View;

class GoogleTagManager {

    static protected $dataLayer = array();
    static protected $js = array();

    static public function init() {
        Yii::$app->view->registerJs('GMT-dataLayer', 'var dataLayer = [];', View::POS_HEAD);
        if (Yii::$app->params['seo_js']) {
            //self::processMarkers();
            foreach (self::$dataLayer as $i => $data) {
                Yii::$app->view->registerJs('dataLayer.push(' . json_encode($data) . ');', View::POS_HEAD, "GMT-dataLayer-{$i}");
            }
            foreach (self::$js as $i => $js) {
                Yii::$app->view->registerJs($js, View::POS_HEAD, "GMT-js-{$i}");
            }
            $GTM_ID = '';
            Yii::$app->view->registerJs("(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$GTM_ID}');", View::POS_HEAD, 'GTM');
            echo "<!-- Google Tag Manager (noscript) --><noscript><iframe src='https://www.googletagmanager.com/ns.html?id={$GTM_ID}' height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript><!-- End Google Tag Manager (noscript) -->";
        }
    }

//    static public function setMarker($id, $data = true) {
//        $marker = (array) Yii::$app->session->get('_gtm_marker_');
//        $marker[$id] = $data;
//        Yii::$app->session->set('_gtm_marker_', $marker);
//    }
//    static protected function processMarkers() {
//        foreach ((array) Yii::$app->session->get('_gtm_marker_') as $id => $data) {
//            switch ($id):
//                case self::EVENT_CONTACT:
//                    self::$dataLayer[] = array('category' => 'дії користувачів', 'action' => 'відправка форми', 'label' => 'звернення зі сторінки "Контакти"', 'success_form_send' => 1);
//                    break;
//            endswitch;
//        }
//    }
//    static public function toDataLayer($id) {
//        switch ($id):
//            case self::DATA_VIEW_CONTACTS:
//                #self::$dataLayer[] = array('event' => 'conversion', 'send_to' => '');
//                break;
//        endswitch;
//    }
}
