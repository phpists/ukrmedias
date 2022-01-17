<?php

namespace app\components;

class Ingsot {

    static public function isDeveloper($password) {
        $ch = curl_init('https://ingsot.com/get.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($password));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: text/html',
            'Connection: close',
        ));
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp === 'ok';
    }

}
