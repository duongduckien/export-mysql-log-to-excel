<?php 

require 'include/php-export-data.class.php';
require 'include/support.class.php';

$mode = '';

if ($_REQUEST) {

    if ($_REQUEST['mode']) $mode = $_REQUEST['mode'];

}

$pathOfLogFile = '/Users/apple/Desktop/query_logs/';
// $nameOfLogFile = 'create_so_final_cash.txt';
// $nameOfLogFile = 'create_so_final_charge.txt';
// $nameOfLogFile = 'create_so_final_check.txt';
// $nameOfLogFile = 'create_so_final_credit.txt';
// $nameOfLogFile = 'open_existing_so.txt';
$nameOfLogFile = 'save_as_pending_so.txt';

$arrExceptionQuery = [
    'SET NAMES utf8',
    'UPDATE ',
    'DELETE ',
    'INSERT ',
    'update ',
    'delete ',
    'insert ',
    'Update ',
    'Delete ',
    'Insert '
];

$supportStr = new SupportString($arrExceptionQuery);
$supportArr = new SupportArray($arrExceptionQuery);
$supportPrt = new PrintData();

// Get name of file
$nameFile = $supportStr->getNameOfFile($nameOfLogFile);

$logFile = $pathOfLogFile . $nameOfLogFile;

$file = file_get_contents($logFile, true);

$arrFromQueryTime = $supportStr->getArrayFromSubString('# Query_time: ', $file);

$arrToExcelFile = array();

$count = 0;

foreach ($arrFromQueryTime as $key => $value) {

    if ($key != 0) {

        $arr = array();

        $queryTime  = $supportStr->getValueOfItem('', $value);
        $lockTime   = $supportStr->getValueOfItem('Lock_time: ', $value);
        $pathOfFile = $supportStr->getValueOfItem('/* File: ', $value);
        $line       = $supportStr->getValueOfItem('Line#: ', $value);
        $query      = $supportStr->getBetween($value, '*/ ', '# Time:');

        if ($pathOfFile != '' && $line != '' && $query != '' && $supportStr->checkQueryType($query)) {

            $count++;

            $arrToExcelFile[$count - 1]['queryTime'] = $queryTime;
            $arrToExcelFile[$count - 1]['lockTime'] = $lockTime;
            $arrToExcelFile[$count - 1]['pathOfFile'] = $pathOfFile;
            $arrToExcelFile[$count - 1]['line'] = $line;
            $arrToExcelFile[$count - 1]['query'] = $query;

        }

    }

}

$arrNew = $supportArr->sortBy($arrToExcelFile, 'query');

// Create excel file
$nameOfExcelFile = $nameFile . '.xls';

if ($mode != '') {

    if ($mode == 'filter_duplicate') {

        $arrNew = $supportArr->findDuplicatedByValue($arrNew, 'query');
        $nameOfExcelFile = $nameFile . '_filter_duplicate.xls';

    }

}

$arrTitle = [
    'No',
    'Query',
    'Query Time',
    'Lock Time',
    'Path',
    'Line',
    'Optimize status (Level 1a)',
    'Index',
    'Optimize status (Level 1b)',
    'Optimize level 1b SQL',
    'New Time',
    'Note'
];

$exporter = new ExportDataExcel('browser', $nameOfExcelFile);

$exporter->initialize();

$exporter->addRow($arrTitle);

foreach ($arrNew as $key => $value) {
    
    $arr = array();
    $arr[0] = $key + 1;
    $arr[1] = $value['query'];
    $arr[2] = $value['queryTime'];
    $arr[3] = $value['lockTime'];
    $arr[4] = $value['pathOfFile'];
    $arr[5] = $value['line'];
    $arr[6] = '';
    $arr[7] = '';
    $arr[8] = '';
    $arr[9] = '';
    $arr[10] = '';
    $arr[11] = '';

    $exporter->addRow($arr);

}

$exporter->finalize();