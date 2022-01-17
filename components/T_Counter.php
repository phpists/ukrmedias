<?php

namespace app\components;

use app\models\Calculator;

trait T_Counter {

    public function getValue() {
        return round(call_user_func_array([$this, "unit_{$this->device_unit}"], [(float) $this->value, (float) $this->device_rate]) + (float) $this->device_sync, Calculator::ROUND_QTY); //Devices::$units
    }

    static protected function unit_0($value, $rate) {
        return $value * pow(10, $rate); //raw
    }

    static protected function unit_1($value, $rate) {
        return $value * pow(10, $rate) / 1000; //литр=>куб.м
    }

    static protected function unit_2($value, $rate) {
        return $value * pow(10, $rate) * 859.84523 / 1000000000; //ватт=>гигакалории
    }

    static protected function unit_3($value, $rate) {
        return $value * pow(10, $rate) / 1000000000; //гигакалории
    }

    static public function getSyncValue($unit, $counterValue, $rate, $deviceValue) {
        $value = call_user_func_array([__CLASS__, "unit_{$unit}"], [$deviceValue, $rate]);
        return round($counterValue - $value, Calculator::ROUND_QTY);
    }

}
