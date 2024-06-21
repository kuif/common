<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2020-04-06 20:49:34
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2020-04-07T12:53:31+08:00
 */
namespace feng;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel
{
    public static function Export($data, $fieldArr=[], $name = '测试表')
    {
        $data = [
            [ 'id'=>1, 'name'=>'张三', 'sex'=>'男', 'age'=>20, 'tel'=>1111, 'ceshi'=>'测试' ],
            [ 'id'=>2, 'name'=>'李四', 'sex'=>'女', 'age'=>18, 'tel'=>2222, 'ceshi'=>'测试' ],
            [ 'id'=>3, 'name'=>'王五', 'sex'=>'女', 'age'=>25, 'tel'=>3333, 'ceshi'=>'测试' ],
            [ 'id'=>4, 'name'=>'赵六', 'sex'=>'男', 'age'=>22, 'tel'=>4444, 'ceshi'=>'测试' ],
        ];

        // $fieldArr = ['ID'=>'id', '姓名'=>'name', '性别'=>'sex', '年龄'=>'age', '电话'=>'tel', '测试'=>'ceshi']; // 声明对应字段关系
        $newExcel = new Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle($name);  //设置当前sheet的标题
        $lines = 1;

        $fieldArr = [ 'id' => 'ID', 'name' => '姓名', 'sex' => '性别', 'age' => '年龄', 'tel' => '电话', 'ceshi' => '测试' ]; // 声明对应字段关系

        $value_field = $fieldArr ? array_values($fieldArr) : array_keys($data[0]);
        foreach ($value_field as $k => $v) {
            $letter = num_letter($k+1);
            $newExcel->getActiveSheet()->getColumnDimension($letter)->setAutoSize(true); // 简单设置列宽
            $objSheet = $objSheet->setCellValue($letter . $lines, $v); // 设置标题
        }

        // 第二行起，每一行的值，setCellValueExplicit是用来导出文本格式的。
        // $objSheet->setCellValueExplicit('A1', '超长数字', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // 可以用来导出数字不变格式（设置单元格格式为纯文本）
        $key_field = $fieldArr ? array_keys($fieldArr) : array_keys($data[0]);
        foreach ($data as $k => $v) { // 数据循环处理
            $lines ++;
            // $newExcel->getActiveSheet()->mergeCells('A19:A22'); // 数组合并
            foreach ($key_field as $m => $n) {
                $letter = self::num_letter($m+1);

                if (($n == 'avatar' || $n == 'image' || $n == 'images') && !empty($v[$n])) {
                    $file = str_replace(SITE_URL, '.', $v[$n]);
                    $file = strstr($file, ',') ? explode(',', $file) : [$file];
                    // dump($file);
                    foreach ($file as $x => $y) {
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setName('Image');
                        $drawing->setDescription('Image');
                        $drawing->setPath($y);
                        $drawing->setWidth(80);
                        $drawing->setHeight(80);
                        $drawing->setCoordinates($letter . $lines);
                        $drawing->setOffsetX(12 + $x*80);
                        $drawing->setOffsetY(12);
                        $drawing->setWorksheet($newExcel->getActiveSheet());
                    }

                } else {
                    $objSheet = $objSheet->setCellValue($letter . $lines, $v[$n]);
                }
            }

            $objSheet->getRowDimension($lines)->setRowHeight(80);
        }

        // self::downloadExcel($newExcel, '测试表', 'Xlsx', './public'); // 保存到本地
        self::downloadExcel($newExcel, $name, 'Xlsx'); //生成文件直接下载
    }

    // 公共文件，十进制转二十六进制(基数为A-Z)
    public static function num_letter($num) {
        $num = intval($num);
        if ($num <= 0)
            return false;
        $letterArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $letter = '';
        do {
            $key = ($num - 1) % 26;
            $letter = $letterArr[$key] . $letter;
            $num = floor(($num - $key) / 26);
        } while ($num > 0);
        return $letter;
    }

    // 公共文件，用来传入xls并下载
    public static function downloadExcel($newExcel, $filename, $format, $savePath = false)
    {
        if(!$savePath){   //网页下载
            // $format只能为 Xlsx 或 Xls
            if ($format == 'Xlsx') {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            } elseif ($format == 'Xls') {
                header('Content-Type: application/vnd.ms-excel');
            }
            //
            header("Content-Disposition: attachment;filename=" . $filename . date('Y-m-d') . '.' . strtolower($format));
            header('Cache-Control: max-age=0');
            $objWriter = IOFactory::createWriter($newExcel, $format);
            $objWriter->save('php://output');
        } else {
            ob_clean();
            ob_start();
            $objWriter = IOFactory::createWriter($newExcel, $format);
            $savePath = './' . trim(ltrim($savePath, '.'), '/') . '/' . $filename . date('Y-m-d') . '.' . strtolower($format);
            $objWriter->save($savePath);
            /* 释放内存 */
            $newExcel->disconnectWorksheets();
            unset($newExcel);
            ob_end_flush();
        }
    }

}
