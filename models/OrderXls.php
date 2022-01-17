<?php

namespace app\models;

use \Yii;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class OrderXls {

    static protected $order;
    static protected $sheet;

    static protected function setHeader($labels, $header) {
        self::$sheet->setCellValue('A1', 'Замовлення №' . self::$order->getNumber() . ' від ' . self::$order->getDate());
        self::$sheet->mergeCells('A1:G1');
        self::$sheet->getStyle('A1:G1')->applyFromArray(['font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);
        self::$sheet->mergeCells('A2:G2');
        foreach ($labels as $i => $info) {
            $row = $i + 3;
            foreach ($info as $label => $value) {
                self::$sheet->setCellValueByColumnAndRow(1, $row, $label);
                self::$sheet->setCellValueByColumnAndRow(2, $row, $value);
            }
        }
        self::$sheet->getStyle("A3:B{$row}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
        ]);
        $row++;
        self::$sheet->mergeCells("A{$row}:G{$row}");
        $row++;
        $xRow = $row;
        foreach ($header as $i => $label) {
            if (is_array($label)) {
                foreach ($label as $title => $rows) {
                    $col = Coordinate::stringFromColumnIndex($i + 1);
                    $col2 = Coordinate::stringFromColumnIndex($i + count($rows));
                    self::$sheet->setCellValue("{$col}{$row}", $title);
                    self::$sheet->mergeCells("{$col}{$row}:{$col2}{$row}");
                    $row++;
                    foreach ($rows as $ii => $value) {
                        $col = Coordinate::stringFromColumnIndex($i + $ii + 1);
                        self::$sheet->setCellValue("{$col}{$row}", $value);
                    }
                }
            } else {
                $col = Coordinate::stringFromColumnIndex($i + 1);
                self::$sheet->setCellValue("{$col}{$row}", $label);
                self::$sheet->mergeCells("{$col}{$row}:{$col}" . ($row + 1));
            }
        }
        self::$sheet->getStyle("A{$xRow}:G{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ],
        ]);
        return ++$row;
    }

    static public function create($file, $order) {
        $spreadsheet = new Spreadsheet();
        self::$order = $order;
        self::$sheet = $spreadsheet->getActiveSheet();
        self::$sheet->setTitle("Замовлення №{$order->getNumber()}");
        $row = $startRow = self::setHeader([
                    ['Клієнт' => $order->getFirmTitle()],
                    ['Оплата' => $order->payment_title],
                    ['Доставка' => $order->delivery_title],
                    ['Область' => $order->region],
                    ['Населений пункт' => $order->city],
                    ['Адреса' => $order->address],
                    ['Вантажоотримувач' => $order->consignee],
                    ['Контактна особа' => $order->name],
                    ['Телефон' => $order->phone],
                    ['Коментар клієнта' => $order->client_note],
                    ['Коментар менеджера' => $order->manager_note],
                    ['Експрес-накладна' => $order->getNP_summary()],
                        ], ['Торгова марка', 'Найменування', 'Артикул', 'Код', 'Кількість', 'Ціна за од.', 'Сума']) + 1;

        for ($i = 1; $i < 15; $i++) {
            self::$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }
        foreach ($order->getDetails() as $dataModel) {
            self::$sheet->setCellValueExplicit("A{$row}", $dataModel->brand, DataType::TYPE_STRING);
            self::$sheet->setCellValueExplicit("B{$row}", $dataModel->getAdminTitle(' / '), DataType::TYPE_STRING);
            self::$sheet->setCellValueExplicit("C{$row}", $dataModel->article, DataType::TYPE_STRING);
            self::$sheet->setCellValueExplicit("D{$row}", $dataModel->code, DataType::TYPE_STRING);
            self::$sheet->setCellValueExplicit("E{$row}", $dataModel->qty, DataType::TYPE_NUMERIC);
            self::$sheet->setCellValueExplicit("F{$row}", $dataModel->price, DataType::TYPE_NUMERIC);
            self::$sheet->setCellValueExplicit("G{$row}", $dataModel->getAmount(), DataType::TYPE_NUMERIC);
            $row++;
        }
        $lastRow = $row - 1;
        if ($row > $startRow) {
            self::$sheet->setCellValue("F{$row}", 'Всього:');
            self::$sheet->setCellValueExplicit("G{$row}", "=SUM(G{$startRow}:G{$lastRow})", DataType::TYPE_FORMULA);
            self::$sheet->getStyle("F{$startRow}:G{$row}")->getNumberFormat()->setFormatCode('0.00');
            self::$sheet->getStyle("E{$row}:G{$row}")->applyFromArray(['font' => ['bold' => true]]);
            self::$sheet->freezePane("A{$startRow}");
            self::$sheet->setAutoFilter("A" . ($startRow - 1) . ":G{$lastRow}");
        }
        self::$sheet->getStyle("A{$startRow}:G{$row}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $writer = new Xlsx($spreadsheet);
        return $writer->save($file);
    }

}
