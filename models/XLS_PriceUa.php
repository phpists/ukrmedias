<?php

namespace app\models;

use \Yii;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class XLS_PriceUa {

    static protected $sheet;

    static protected function setHeader($header) {
        $row = 1;
        $c = 1;
        foreach ($header as $c => $info) {
            self::$sheet->setCellValueByColumnAndRow($c + 1, $row, $info);
        }
        $col = Coordinate::stringFromColumnIndex($c + 1);
        self::$sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ],
        ]);
        $row++;
        return $row;
    }

    static public function create($file, $title, $filterData) {
        try {
            $spreadsheet = new Spreadsheet();
            self::$sheet = $spreadsheet->getActiveSheet();
            self::$sheet->setTitle($title);
            $header = [];
            foreach (array_keys($filterData) as $attr) {
                if (isset(XML_PriceUa::$attrs[$attr])) {
                    $header[] = XML_PriceUa::$attrs[$attr];
                }
            }
            $maxCol = Coordinate::stringFromColumnIndex(count($header));
            $row = $startRow = self::setHeader($header) + 1;

            for ($i = 1; $i < count($header); $i++) {
                self::$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }
            $category = Category::findModel(@$filterData['cat_id']);
            $filter = new GoodsFilter($category);
            foreach ($filter->getQuery(@$filterData['price_type_id'])->all() as $model) {
                $values = [];
                if (isset($filterData['name'])) {
                    $values[] = $model->getTitle();
                }
                if (isset($filterData['categoryId'])) {
                    $values[] = $model->getCategory()->title;
                }
                if (isset($filterData['priceuah'])) {
                    $values[] = $model->getBasePrice();
                }
                if (isset($filterData['image'])) {
                    $photo = $model->getMainPhoto();
                    if ($photo !== null) {
                        $values[] = $photo->getSrc();
                    } else {
                        $values[] = '';
                    }
                }
                if (isset($filterData['vendor'])) {
                    $values[] = $model->getBrand()->title;
                }
                if (isset($filterData['vendorCode'])) {
                    $values[] = $model->article;
                }
                if (isset($filterData['param'])) {
                    $p = [];
                    foreach ($model->getParams(Params::TYPE_GOODS_ONLY)as $param) {
                        $p[] = "{$param->getTitle()}: {$param->getValue()} {$param->getUnit()};";
                    }
                    $values[] = implode(PHP_EOL, $p);
                }
                if (isset($filterData['description'])) {
                    $values[] = $model->getDescr();
                }
                if (isset($filterData['available'])) {
                    $values[] = $model->getAvailableXls();
                }
                foreach ($values as $c => $value) {
                    self::$sheet->setCellValueExplicitByColumnAndRow($c + 1, $row, $value, DataType::TYPE_STRING);
                }
                $row++;
            }
            $lastRow = $row - 1;
            if ($row > $startRow) {
                self::$sheet->freezePane("A{$startRow}");
                self::$sheet->setAutoFilter("A" . ($startRow - 1) . ":{$maxCol}{$lastRow}");
            }
            self::$sheet->getStyle("A{$startRow}:{$maxCol}{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $writer = new Xlsx($spreadsheet);
            $writer->save($file);
            return true;
        } catch (\Throwable $ex) {
            Yii::dump("{$ex->getFile()}({$ex->getLine()}):\n{$ex->getMessage()}");
            return false;
        }
    }

}
