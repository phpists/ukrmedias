<?php

namespace app\models;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;

class XLS_PriceUa
{

    static protected $sheet;

    static protected function setHeaderTitle()
    {
        $row = 1;
        $c = 1;

        self::$sheet->setCellValueByColumnAndRow($c, $row, 'Залишки контрагентів для замовлення');
        $col = Coordinate::stringFromColumnIndex($c);
        self::$sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
            'color' => ['argb' => 'FFFF0000'],
        ]);
        $row++;
        return $row;
    }

    static protected function setHeaderDate()
    {
        $row = 3;
        $c = 1;

        self::$sheet->setCellValueByColumnAndRow($c, $row, 'Залишки на дату: ' . date('d.m.Y'));
        $col = Coordinate::stringFromColumnIndex($c);
        self::$sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
            'font' => ['size' => 8],
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
            'color' => ['argb' => 'FFFF0000'],
        ]);
        $row++;
        return $row;
    }

    static protected function setHeader($column, $header)
    {
        $row = 5;
        $c = 1;
        foreach ($header as $c => $info) {
            self::$sheet->setCellValueByColumnAndRow($c + 1, $row, $info);
        }
        $col = Coordinate::stringFromColumnIndex($c + 1);
        self::$sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 8],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'color' => [
                    'argb' => 'FFF6EE8B',
                ],
            ],
        ]);

        self::setOrderColumnColor($column, $row);

        return $row;
    }

    static function setOrderColumnColor($col, $row)
    {
        self::$sheet->getStyle("{$col}{$row}")->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'color' => [
                    'argb' => 'FFACFF6F',
                ],
            ],
        ]);
    }

    static public function create($file, $title, $filterData)
    {
        try {
            $modelsItems = Yii::$app->cache->get("_download_model_goods");

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(10);


            self::$sheet = $spreadsheet->getActiveSheet();
            self::$sheet->setTitle($title);
            $header = [];
            foreach (array_keys($filterData) as $attr) {
                if (isset(XML_PriceUa::$attrs[$attr])) {
                    $header[] = XML_PriceUa::$attrs[$attr];
                }
            }
            $maxCol = Coordinate::stringFromColumnIndex(count($header));

            self::setHeaderTitle() + 1;
            self::setHeaderDate() + 1;
            $row = $startRow = self::setHeader('G', $header) + 1;


            for ($i = 1; $i < count($header); $i++) {
                self::$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }

            foreach ($modelsItems as $model) {

                $values = [];
                if (isset($filterData['name'])) {
                    $values[] = $model->getTitle();
                }
                if (isset($filterData['vendorCode'])) {
                    $values[] = $model->article;
                }
                if (isset($filterData['code'])) {
                    $values[] = '';
                }
                if (isset($filterData['vendor'])) {
                    $values[] = $model->getBrand()->title;
                }
                if (isset($filterData['visible_by_stock'])) {
                    $values[] = $model->getVisibleStockCount();
                }
                if (isset($filterData['priceuah'])) {
                    $values[] = $model->getPriceRange();
                }
                if (isset($filterData['odrder'])) {
                    $values[] = '';
                }
                if (isset($filterData['image'])) {
                    $photo = $model->getMainPhoto();
                    if ($photo !== null) {
                        $values[] = Yii::$app->request->getHostInfo() . $photo->getSrc();
                    } else {
                        $values[] = '';
                    }
                }
                if (isset($filterData['categoryId'])) {
                    $values[] = $model->getCategory()->title;
                }

                if (isset($filterData['param'])) {
                    $p = [];
                    foreach ($model->getParams(Params::TYPE_GOODS_ONLY) as $param) {
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
                    self::$sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'color' => [
                                'argb' => 'FFFFFADA',
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                        ],
                    ]);

                    self::setOrderColumnColor('G', $row);
                }

                $row++;

                foreach ($model->getVisibleStockItems() as $valueItem) {
                    foreach ($valueItem as $c => $value) {
                        self::$sheet->setCellValueExplicitByColumnAndRow($c + 1, $row, $value, DataType::TYPE_STRING);
                        self::$sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFFFADA'],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                            ],
                        ]);

                        self::setOrderColumnColor('G', $row);

                        $spreadsheet->getActiveSheet()->getRowDimension($row)->setOutlineLevel(1);
                        $spreadsheet->getActiveSheet()->getRowDimension($i)->setVisible(true);
                    }
                    $row++;
                }

            }

            self::$sheet->getStyle("A{$startRow}:{$maxCol}{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $writer = new Xlsx($spreadsheet);
            $writer->save($file);
            return true;
        } catch (\Throwable $ex) {
            Yii::dump("{$ex->getFile()}({$ex->getLine()}):\n{$ex->getMessage()}");
            return false;
        }
    }

    static public function createWithPhoto($file, $title, $filterData)
    {
        try {
            $modelsItems = Yii::$app->cache->get("_download_model_goods");

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(10);

            self::$sheet = $spreadsheet->getActiveSheet();
            self::$sheet->setTitle($title);
            $header = [];
            foreach (array_keys($filterData) as $attr) {
                if (isset(XML_PriceUa::$attrs[$attr])) {
                    $header[] = XML_PriceUa::$attrs[$attr];
                }
            }
            $maxCol = Coordinate::stringFromColumnIndex(count($header));

            self::setHeaderTitle() + 1;
            self::setHeaderDate() + 1;
            $row = $startRow = self::setHeader('H', $header) + 1;

            $spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension("G")->setWidth(17);

            for ($i = 1; $i < count($header); $i++) {

                self::$sheet->getColumnDimensionByColumn($i);
            }

            foreach ($modelsItems as $model) {

                $values = [];
                if (isset($filterData['name'])) {
                    $values[] = $model->getTitle();
                }
                if (isset($filterData['vendorCode'])) {
                    $values[] = $model->article;
                }
                if (isset($filterData['code'])) {
                    $values[] = '';
                }
                if (isset($filterData['vendor'])) {
                    $values[] = $model->getBrand()->title;
                }
                if (isset($filterData['visible_by_stock'])) {
                    $values[] = $model->getVisibleStockCount();
                }
                if (isset($filterData['priceuah'])) {
                    $values[] = $model->getPriceRange();
                }
                if (isset($filterData['odrder'])) {
                    $values[] = '';
                }
                if (isset($filterData['image'])) {
                    $values[] = '';

                    $photo = $model->getMainPhoto();
                    if (file_exists('.' . $photo->getSrc())) {

                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setPath('.' . $photo->getSrc());
                        $drawing->setHeight(95);
                        $drawing->setWidth(115);
                        $drawing->setCoordinates("G{$row}");
                        $drawing->setWorksheet($spreadsheet->getActiveSheet());

                        $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(118);

                    } else {
                        $values[] = '';
                    }

                }
                if (isset($filterData['categoryId'])) {
                    $values[] = $model->getCategory()->title;
                }

                if (isset($filterData['param'])) {
                    $p = [];
                    foreach ($model->getParams(Params::TYPE_GOODS_ONLY) as $param) {
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
                    self::$sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'color' => [
                                'argb' => 'FFFFFADA',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                    ]);

                    self::setOrderColumnColor('H', $row);
                }


                $row++;

                foreach ($model->getVisibleStockItems() as $valueItem) {

                    foreach ($valueItem as $c => $value) {
                        self::$sheet->setCellValueExplicitByColumnAndRow($c + 1, $row, $value, DataType::TYPE_STRING);
                        self::$sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFFFADA'],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                            ],
                        ]);

                        self::setOrderColumnColor('H',$row);

                        $spreadsheet->getActiveSheet()->getRowDimension($row)->setOutlineLevel(1);
                        $spreadsheet->getActiveSheet()->getRowDimension($i)->setVisible(false);

                    }
                    $row++;
                }


            }

            self::$sheet->getStyle("A{$startRow}:{$maxCol}{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
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
