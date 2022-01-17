<?php

namespace app\components;

use yii\helpers\Html;
use \Yii;

class Misc {

    static $months = array(
        '01' => 'Січень',
        '02' => 'Лютий',
        '03' => 'Березень',
        '04' => 'Квітень',
        '05' => 'Травень',
        '06' => 'Червень',
        '07' => 'Липень',
        '08' => 'Серпень',
        '09' => 'Вересень',
        '10' => 'Жовтень',
        '11' => 'Листопад',
        '12' => 'Грудень',
    );
    static $monthsPast = array(
        '01' => 'січні',
        '02' => 'лютому',
        '03' => 'березні',
        '04' => 'квітні',
        '05' => 'травні',
        '06' => 'червні',
        '07' => 'липні',
        '08' => 'серпні',
        '09' => 'вересні',
        '10' => 'жовтні',
        '11' => 'листопаді',
        '12' => 'грудні',
    );

//    static public function readfile($file, $fname = null, $mime = 'application/octet-stream') {
//        if (ob_get_level()) {
//            ob_end_clean();
//        }
//        if ($fname === null) {
//            $fname = basename($file);
//        }
//        header('Content-Description: File Transfer');
//        header("Content-Type: $mime");
//        header("Content-Disposition: attachment; filename={$fname}");
//        header('Content-Transfer-Encoding: binary');
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate');
//        header('Pragma: public');
//        header('Content-Length: ' . filesize($file));
//        readfile($file);
//    }
    static public function sendFile($file, $attachmentName = null, $delete = true) {
        if ($delete) {
            Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, function ($event) {
                if (is_file($event->data)) {
                    unlink($event->data);
                }
            }, $file);
        }
        return Yii::$app->response->sendFile($file, $attachmentName)->send();
    }

    static public function createCpu($string) {
        $dic = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'a', 'Б' => 'b', 'В' => 'v',
            'Г' => 'g', 'Д' => 'd', 'Е' => 'e',
            'Ё' => 'e', 'Ж' => 'zh', 'З' => 'z',
            'И' => 'i', 'Й' => 'y', 'К' => 'k',
            'Л' => 'l', 'М' => 'm', 'Н' => 'n',
            'О' => 'o', 'П' => 'p', 'Р' => 'r',
            'С' => 's', 'Т' => 't', 'У' => 'u',
            'Ф' => 'f', 'Х' => 'h', 'Ц' => 'ts',
            'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sch',
            'Ь' => '', 'Ы' => 'y', 'Ъ' => '',
            'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',
            'і' => 'i', 'ї' => 'i', 'є' => 'e',
            'І' => 'i', 'Ї' => 'i', 'Є' => 'e',
        );
        $string = preg_replace('/[^a-z0-9-]/i', '-', strtr($string, $dic));
        return strtolower(substr(trim(preg_replace('/-{2,}/', '-', $string), '-'), 0, 255));
    }

    static public function years() {
        $data = array();
        for ($i = date('Y') - 2; $i <= date('Y'); $i++) {
            $data[$i] = $i;
        }
        return $data;
    }

    static public function wordingFlats($n) {
        return $n % 10 == 1 && $n % 100 != 11 ? 'приміщення' : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 'приміщення' : 'приміщень');
    }

    static public function wordingGoods($n) {
        return $n % 10 == 1 && $n % 100 != 11 ? 'товар' : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 'товари' : 'товарів');
    }

    static public function wordingDays($n) {
        return $n % 10 == 1 && $n % 100 != 11 ? 'день' : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 'дні' : 'днів');
    }

    static public function wordingHours($n) {
        return $n % 10 == 1 && $n % 100 != 11 ? 'година' : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 'години' : 'годин');
    }

    static public function wordingMinutes($n) {
        return $n % 10 == 1 && $n % 100 != 11 ? 'хвилина' : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 'хвилини' : 'хвилин');
    }

    static public function wordingMinutesAlt($n) {
        return $n % 10 == 1 && $n % 100 != 11 ? 'хвилину' : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 'хвилини' : 'хвилин');
    }

    static public function wordingSecondsAlt($n) {
        return $n % 10 == 1 && $n % 100 != 11 ? 'секунду' : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 'секунди' : 'секунд');
    }

    static public function secondsToString($endSeconds, $startSeconds = 0) {
        $start = new \DateTime(date('Y-m-d H:i', $startSeconds));
        $result = (new \DateTime(date('Y-m-d H:i', $endSeconds)))->diff($start);
        $string = '';
        if ($result->h > 0 || $result->days > 0) {
            $qty = $result->days * 24 + $result->h;
            $string .= $qty . ' ' . self::wordingHours($qty);
        }
        if ($result->i > 0) {
            $string .= ' ' . $result->i . ' ' . self::wordingMinutes($result->i);
        }
        return $string;
    }

    static public function iterator(\yii\db\ActiveQuery $query, $func, $asArray = false, $step = 2000) {
        $offset = 0;
        while ($dataset = $query->offset($offset)->limit($step)->asArray($asArray)->all()) {
            foreach ($dataset as $data) {
                $func($data);
            }
            $offset += $step;
        }
    }

    static public function getFlashMessages() {
        $html = '';
        if (Yii::$app->session->hasFlash('error')) {
            $html .= '<div class="alert alert-danger">' . Yii::$app->session->getFlash('error') . '</div>';
        }
        if (Yii::$app->session->hasFlash('success')) {
            $html .= '<div class="alert alert-success">' . Yii::$app->session->getFlash('success') . '</div>';
        }
        if (Yii::$app->session->hasFlash('info')) {
            $html .= '<div class="alert alert-info">' . Yii::$app->session->getFlash('info') . '</div>';
        }
        if (Yii::$app->session->hasFlash('warning')) {
            $html .= '<div class="alert alert-warning">' . Yii::$app->session->getFlash('warning') . '</div>';
        }
        return $html;
    }

    static public function internalErrorMessage() {
        return "Розробнику вже відомо про помилку і він працює над проблемою. Спробуйте пізніше.\nВибачте за можливу незручність.";
    }

    static public function phoneFilter($phone) {
        return preg_replace('/[^\+\d]+/', '', $phone);
    }

    static public function priceFilter($price) {
        return (float) str_replace([',', ' '], ['.', ''], $price);
    }

    static public function errorSummary($models, $options = []) {
        $header = isset($options['header']) ? $options['header'] : '<p>' . Yii::t('yii', 'Please fix the following errors:') . '</p>';
        $footer = \yii\helpers\ArrayHelper::remove($options, 'footer', '');
        $encode = \yii\helpers\ArrayHelper::remove($options, 'encode', true);
        $showAllErrors = \yii\helpers\ArrayHelper::remove($options, 'showAllErrors', false);
        unset($options['header']);
        $lines = \yii\helpers\BaseHtml::collectErrors($models, $encode, $showAllErrors);
        if (empty($lines)) {
            $content = '';
            $options['style'] = isset($options['style']) ? rtrim($options['style'], ';') . '; display:none' : 'display:none';
        } else {
            $content = implode("<br/>\n", $lines);
        }

        return \yii\helpers\BaseHtml::tag('div', $header . $content . $footer, $options);
    }

    static public function uniqid($prefix = '') {
        return uniqid($prefix . mt_rand(1000000, 9999999), true);
    }

    static public function createKey($prefix = '') {
        return $prefix . mt_rand(100000000, mt_getrandmax());
    }

    static public function round($number, $n = 2) {
        return number_format($number, $n, '.', '');
    }

    static public function TestVersionHeader() {
        return Yii::$app->params['isTest'] ? '<div style="position:fixed;top:0;left:0;width:100%;background-color:rgba(255,0,0,0.5);color:#ffffff;font-size:10px;z-index:1000000;text-align:center;">тестовая версия</div>' : null;
    }

    static public function text2list($text) {
        $data = [];
        foreach (preg_split("/[\r\n]/", $text, null, PREG_SPLIT_NO_EMPTY) as $row) {
            $data[] = $row;
        }
        return $data;
    }

    static public function isTextExists($text) {
        return mb_strlen(strip_tags($text, '<img>')) > 0;
    }

    static public function setMetaData($title, $keywords, $descr) {
        Yii::$app->view->title = $title;
        Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $keywords,
        ]);
        Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $descr,
        ]);
    }

    static public function setMetaSocial($title, $description, $src) {
        if ($src === null) {
            $src = Yii::$app->request->getHostInfo() . '/files/frontend/img/login/ukrmedias.svg';
        }
        //Twitter
        Yii::$app->view->registerMetaTag(['name' => 'twitter:site', 'content' => Yii::$app->name]);
        Yii::$app->view->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
        Yii::$app->view->registerMetaTag(['name' => 'twitter:description', 'content' => $description]);
        Yii::$app->view->registerMetaTag(['name' => 'twitter:image:src', 'content' => $src]);
        Yii::$app->view->registerMetaTag(['name' => 'twitter:domain', 'content' => Yii::$app->request->getHostInfo()]);
        Yii::$app->view->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
        //Facebook
        Yii::$app->view->registerMetaTag(['property' => 'og:site_name', 'content' => Yii::$app->name]);
        Yii::$app->view->registerMetaTag(['property' => 'og:url', 'content' => Yii::$app->request->getAbsoluteUrl()]);
        Yii::$app->view->registerMetaTag(['property' => 'og:title', 'content' => $title]);
        Yii::$app->view->registerMetaTag(['property' => 'og:image', 'content' => $src]);
        Yii::$app->view->registerMetaTag(['property' => 'og:image:secure_url', 'content' => $src]);
        Yii::$app->view->registerMetaTag(['property' => 'og:image:secure_url', 'content' => $src]);
        Yii::$app->view->registerMetaTag(['property' => 'og:type', 'content' => 'website']);
        Yii::$app->view->registerMetaTag(['property' => 'og:description', 'content' => $description]);
    }

    static public function getPhpSessionTimeoutTxt() {
        $n = (ini_get("session.gc_maxlifetime") / 60);
        return $n . ' ' . self::wordingMinutes($n);
    }

    static public function oneTimePassword() {
        return (string) mt_rand(1000, 9999);
    }

    static public function normalizePhone($phone) {
        $phone = preg_replace('/[^\d]+/', '', self::phoneFilter($phone));
        if (strpos($phone, '0') === 0) {
            $phone = "+38{$phone}";
        }
        if (strpos($phone, '+') === false) {
            $phone = "+{$phone}";
        }
        return $phone;
    }

}
