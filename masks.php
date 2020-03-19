<?php

require 'vendor/autoload.php';

use League\CLImate\CLImate;

$url = "http://data.nhi.gov.tw/Datasets/Download.ashx?rid=A21030000I-D50001-001&l=https://data.nhi.gov.tw/resource/mask/maskdata.csv";
$csv = file_get_contents($url);
$records = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $csv));
array_pop($records);

$climate = new CLImate();
$input = $climate->input("請輸入欲查詢的地理區：");
$inputArea = $input->prompt();
$record_datas[] = ["醫事機構名稱", "醫事機構地址", "成人口罩剩餘數"];

foreach ($records as $record) {
    if ($inputArea == "") {
        break;
    }
    if (strpos($record[2], $inputArea) !== false) {
        $record_datas[] = array($record[1], $record[2], $record[4]);
    }
}

usort($record_datas, function ($a, $b) {
    if ($a[2] == $b[2]) {
        return 0;
    }
    return ($a[2] > $b[2]) ? -1 : 1;
});

$climate->table($record_datas);

// 已使用 php phpcbf.phar --standard=PSR12 masks.php 修正格式
