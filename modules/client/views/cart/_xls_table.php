<?php

use \yii\helpers\Html;
use \app\models\ExcelViewer;

$cells = 0;
$rowsLabels = [];
#$max = $sheet->getHighestRowAndColumn();
#var_export($max);
?><table class="cells">
    <tr class="panel">
        <?php
        foreach ($sheet->getRowIterator() as $row):
            $cellIterator = $row->getCellIterator();
            ?>
            <td class="panel"></td>
            <?php
            foreach ($cellIterator as $cell) :
                $cells++;
                if ($cells > 26) {
                    break;
                }
                $selected = isset($settings['col'][$cell->getColumn()]) ? $settings['col'][$cell->getColumn()] : null;
                ?>
                <td class="panel"><?php echo Html::dropDownList("ExcelViewer[col][{$cell->getColumn()}]", $selected, ExcelViewer::$attributes, ['prompt' => '']); ?></td>
            <?php endforeach; ?>
            <?php
            break;
        endforeach;
        ?>
    </tr>
    <tr class="panel">
        <?php
        $cc = 0;
        foreach ($sheet->getRowIterator() as $row):
            $cellIterator = $row->getCellIterator();
            ?>
            <td class="panel"></td>
            <?php
            foreach ($cellIterator as $cell) :
                $cc++;
                if ($cc > $cells) {
                    break;
                }
                ?>
                <td class="panel"><?php echo $cell->getColumn(); ?></td>
            <?php endforeach; ?>
            <?php
            break;
        endforeach;
        ?>
    </tr>
    <?php
    foreach ($sheet->getRowIterator() as $r => $row):
        $cellIterator = $row->getCellIterator();
        if (!$sheet->getRowDimension($r)->getVisible()) {
            continue;
        }
        ?>
        <tr>
            <td class="panel"><?php echo $r; ?></td>
            <?php
            $cc = 0;
            $rowsLabels[$r] = "почати зі строки {$r}";
            foreach ($cellIterator as $cell) :
                $cc++;
                if ($cc > $cells) {
                    break;
                }
                $value = $cell->getValue();
                if ($value === '#NULL!') {
                    $value = '';
                }
                ?>
                <td><?php echo $value; ?></td>
                <?php
            endforeach;
            if ($cells > $cc) {
                echo '<td colspan="' . ($cells - $cc) . '"></td>';
            }
            ?>
        <?php endforeach; ?>
</table>
<div class="sheets">
    <?php echo Html::dropDownList("ExcelViewer[start_row]", $settings['start_row'], $rowsLabels); ?>
</div>
