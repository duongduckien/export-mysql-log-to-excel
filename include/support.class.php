<?php 

abstract class Support {

    protected $arrExceptionQuery;

    public function __construct($arrExceptionQuery = []) {

        $this->arrExceptionQuery = $arrExceptionQuery;

    }

}

class SupportString extends Support {

    // Get name of file
    function getNameOfFile($name) {
        return explode('.', $name)[0];
    }

    // Convert string to array by sub string
    function getArrayFromSubString($subStr, $str) {
        return explode($subStr, $str);
    }

    // Get sub string in string
    function getValueOfItem($subStr, $str) {

        if ($subStr == '') {
            return explode(' ', $str)[0];
        }

        if (!$this->checkSubStrExist($subStr, $str)) {
            return '';
        }
    
        $arrFromSubStr = explode($subStr, $str);
    
        return explode(' ', $arrFromSubStr[1])[0];

    }

    // Get sub string between two characters
    function getBetween($content, $start, $end) {
        
        $r = explode($start, $content);
        
        if (isset($r[1])){
            $r = explode($end, $r[1]);
            return $r[0];
        }
    
        return '';
    
    }

    // Check query string have exception or not
    function checkQueryType($query) {

        if (sizeof($this->arrExceptionQuery) > 0) {

            $arrCheck = [];
            
            foreach ($this->arrExceptionQuery as $value) {
                if ($this->checkSubStrExist($value, $query)) {
                    array_push($arrCheck, true);
                }
            }

            if (sizeof($arrCheck) == 0) {
                return true;
            }

            return false;

        }

        return true;

    }

    // Check sub string exist in string
    private function checkSubStrExist($subStr, $str) {
        
        if (strpos($str, $subStr) !== false) {
            return true;
        }
    
        return false;
    
    }

}

class SupportArray extends Support {

    function sortBy($arr, $key) {

        usort($arr, $this->buildSorter($key));

        return $arr;

    }

    function buildSorter($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }

    function findDuplicatedByValue($arr, $name) {

        $arrNew = array_column($arr, $name);

        $arrKeyDuplicated = array();
        $arrValueDuplicated = array();

        foreach ($arr as $key => $value) {

            $arrCheck = [];

            foreach ($arrNew as $k => $v) {

                if ($v == $value[$name]) {
                    array_push($arrCheck, true);
                }

            }

            if (count($arrCheck) > 1) {

                if (!in_array($key, $arrKeyDuplicated) && !in_array($value[$name], $arrValueDuplicated)) {
                    array_push($arrKeyDuplicated, $key);
                    array_push($arrValueDuplicated, $value[$name]);
                }
                
            }

        }

        $arrDataDuplicated = array();

        foreach ($arrKeyDuplicated as $item) {

            array_push($arrDataDuplicated, $arr[$item]);

        }

        return $arrDataDuplicated;

    }

}

// Support print to easy see
class PrintData {

    // Print string
    function printString($string) {
        echo "<br>";
        echo "********************************";
        print_r($string);
        echo "********************************";
        echo "<br>";
    }

    // Print array
    function printArray($arr) {
        echo "<pre>";
        print_r($arr);
    }

}