<?php

namespace app\models;

class Factory {

    static public $calculatorClass = '\app\models\Calculator';
    static public $calculator;

    static public function getCalculator() {
        if (self::$calculator === null) {
            self::$calculator = new self::$calculatorClass;
        }
        return self::$calculator;
    }

    static public function setCalculatorModel($id) {
        switch ($id):
            case Services::ID_COOL_WATER:
                self::$calculatorClass = '\app\models\CalculatorTestCoolWater';
                break;
            case Services::ID_HOT_WATER:
                self::$calculatorClass = '\app\models\CalculatorTestHotWater';
                break;
            case Services::ID_HEAT:
                self::$calculatorClass = '\app\models\CalculatorTestHeat';
                break;
            default:
                self::$calculatorClass = $id;
        endswitch;
    }

}
