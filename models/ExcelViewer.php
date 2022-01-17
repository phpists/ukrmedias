<?php

namespace app\models;

use \Yii;
use app\components\RuntimeFiles;

class ExcelViewer {

    static protected $fname;
    static protected $fname_settings;
    static public $attributes = [
        'bar_code' => 'штрих-код',
        'qty' => 'кількість',
    ];

    static public function getFname() {
        if (self::$fname === null) {
            self::$fname = RuntimeFiles::getUid('userupload', "file.xls");
        }
        return self::$fname;
    }

    static public function getFnameSettings() {
        if (self::$fname_settings === null) {
            self::$fname_settings = RuntimeFiles::getUid('userupload', "file.xls.json");
        }
        return self::$fname_settings;
    }

    static public function getPreviewData($settings) {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile(self::getFname());
            $reader->setReadDataOnly(true);
            $reader->setReadFilter(new MyReadFilter());
            $spreadsheet = $reader->load(self::getFname());
            if ($spreadsheet->getSheetCount() === 0) {
                return ['section' => 'empty'];
            }
        } catch (\Throvable $ex) {
            Yii::dump("actionImportExcel: {$ex->getFile()}({$ex->getLine()}):\n{$ex->getMessage()}\n" . self::getFname());
            return ['section' => 'empty'];
        }
        if ($settings === null && is_file(self::getFnameSettings())) {
            $settings = json_decode(file_get_contents(self::getFnameSettings()), true);
        }
        if (!isset($settings['sheet'])) {
            $settings['sheet'] = 0;
        }
        if (!isset($settings['start_row'])) {
            $settings['start_row'] = 1;
        }
        $index = $settings['sheet'] > $spreadsheet->getSheetCount() - 1 ? 0 : $settings['sheet'];
        return [
            'section' => 'sheets',
            'sheets' => $reader->listWorksheetNames(self::getFname()),
            'active' => $settings['sheet'],
            'sheet' => $spreadsheet->getSheet($index),
            'settings' => $settings,
        ];
    }

    static public function getData($settings) {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile(self::getFname());
            $reader->setReadDataOnly(true);
            //$reader->setReadFilter(new MyReadFilter());
            $spreadsheet = $reader->load(self::getFname());
            if ($spreadsheet->getSheetCount() === 0) {
                return [];
            }
        } catch (\Throvable $ex) {
            Yii::dump("actionImportExcel: {$ex->getFile()}({$ex->getLine()}):\n{$ex->getMessage()}\n" . self::getFname());
            return [];
        }
        file_put_contents(self::getFnameSettings(), json_encode($settings));
        $activeIndex = isset($settings['sheet']) ? $settings['sheet'] : 0;
        $startRow = isset($settings['start_row']) ? $settings['start_row'] : 1;
        $data = [];
        foreach ($spreadsheet->getSheet($activeIndex)->getRowIterator() as $r => $row) {
            if ($r < $startRow) {
                continue;
            }
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                $c = $cell->getColumn();
                if (!isset($settings['col'][$c])) {
                    continue;
                }
                $attr = $settings['col'][$c];
                if (empty($attr) || !array_key_exists($attr, self::$attributes)) {
                    continue;
                }
                $value = trim($cell->getValue());
                if ($value === '#NULL!') {
                    $value = '';
                }
                $data[$r][$attr] = $value;
            }
        }
        return $data;
    }

}

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        if ($row < 30) {
            return true;
        }
        return false;
    }

}
