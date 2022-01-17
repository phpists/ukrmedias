<?php

namespace app\components;

class Browser {

    const APPLE_PHONE = 'iPhone';
    const APPLE_IPOD = 'iPod';
    const APPLE_TABLET = 'iPad';
    const ANDROID_PHONE = '(?=.*\bAndroid\b)(?=.*\bMobile\b)';
    const ANDROID_TABLET = 'Android';
    const WINDOWS_PHONE = 'IEMobile';
    const WINDOWS_TABLET = '(?=.*\bWindows\b)(?=.*\bARM\b)';
    const OTHER_BLACKBERRY = 'BlackBerry';
    const OTHER_BLACKBERRY_10 = 'BB10';
    const OTHER_OPERA = 'Opera Mini';
    const OTHER_FIREFOX = '(?=.*\bFirefox\b)(?=.*\bMobile\b)';
    const SEVEN_INCH = '(?:Nexus 7|BNTV250|Kindle Fire|Silk|GT-P1000)';

    static protected $is = array();

    static protected function name() {
        return getenv('HTTP_USER_AGENT');
    }

    static protected function is($key) {
        if (!isset(self::$is[$key])) {
            self::$is[$key] = preg_match('/' . $key . '/i', self::name());
        }
        return self::$is[$key];
    }

    static public function isMobile() {
        return self::is(self::APPLE_PHONE) || self::is(self::APPLE_IPOD) || self::is(self::APPLE_TABLET) || self::is(self::ANDROID_PHONE) || self::is(self::ANDROID_TABLET) || self::is(self::WINDOWS_PHONE) || self::is(self::WINDOWS_TABLET) || self::is(self::OTHER_BLACKBERRY) || self::is(self::OTHER_BLACKBERRY_10) || self::is(self::OTHER_OPERA) || self::is(self::OTHER_FIREFOX) || self::is(self::SEVEN_INCH);
    }

    static public function isWide() {
        return !self::isMobile() || self::is(self::APPLE_TABLET) || !self::is(self::ANDROID_PHONE) && self::is(self::ANDROID_TABLET) || self::is(self::WINDOWS_TABLET);
    }

}
